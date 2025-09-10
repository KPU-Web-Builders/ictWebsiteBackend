<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ContactMessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ContactMessage::query();

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by service interest
        if ($request->has('service_interest')) {
            $query->byService($request->service_interest);
        }

        // Filter by budget range
        if ($request->has('budget_range')) {
            $query->byBudget($request->budget_range);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        // Filter by recent days
        if ($request->has('recent_days')) {
            $query->recent($request->recent_days);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Order by newest first
        $query->orderBy('created_at', 'desc');

        // Pagination
        $messages = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    public function show($id): JsonResponse
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact message not found'
            ], 404);
        }

        // Mark as read if it's new
        if ($message->status === ContactMessage::STATUS_NEW) {
            $message->update(['status' => ContactMessage::STATUS_READ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $message
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:100',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string',
            'service_interest' => 'nullable|string|max:100',
            'budget_range' => 'nullable|string|max:50',
            'preferred_contact' => ['nullable', Rule::in(['email', 'phone', 'both'])],
        ]);

        // Add IP address and User Agent automatically
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        $contactMessage = ContactMessage::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Contact message submitted successfully',
            'data' => $contactMessage
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $contactMessage = ContactMessage::find($id);

        if (!$contactMessage) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact message not found'
            ], 404);
        }

        $validated = $request->validate([
            'status' => ['sometimes', Rule::in(['new', 'read', 'replied', 'closed'])],
            'admin_notes' => 'nullable|string',
            'replied_at' => 'nullable|date'
        ]);

        // If status is being set to replied and no replied_at is provided, set it to now
        if (isset($validated['status']) && $validated['status'] === ContactMessage::STATUS_REPLIED && !isset($validated['replied_at'])) {
            $validated['replied_at'] = now();
        }

        $contactMessage->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Contact message updated successfully',
            'data' => $contactMessage->fresh()
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact message not found'
            ], 404);
        }

        $message->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Contact message deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact message not found'
            ], 404);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['new', 'read', 'replied', 'closed'])],
            'admin_notes' => 'nullable|string'
        ]);

        $updateData = ['status' => $validated['status']];

        // Set replied_at when status changes to replied
        if ($validated['status'] === ContactMessage::STATUS_REPLIED) {
            $updateData['replied_at'] = now();
        }

        // Add admin notes if provided
        if (isset($validated['admin_notes'])) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }

        $message->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Status updated successfully',
            'data' => $message->fresh()
        ]);
    }

    public function getStats(): JsonResponse
    {
        $stats = [
            'total' => ContactMessage::count(),
            'new' => ContactMessage::new()->count(),
            'read' => ContactMessage::read()->count(),
            'replied' => ContactMessage::replied()->count(),
            'closed' => ContactMessage::closed()->count(),
            'today' => ContactMessage::whereDate('created_at', today())->count(),
            'this_week' => ContactMessage::recent(7)->count(),
            'this_month' => ContactMessage::recent(30)->count(),
        ];

        // Top services requested
        $topServices = ContactMessage::whereNotNull('service_interest')
            ->selectRaw('service_interest, COUNT(*) as count')
            ->groupBy('service_interest')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Budget range distribution
        $budgetDistribution = ContactMessage::whereNotNull('budget_range')
            ->selectRaw('budget_range, COUNT(*) as count')
            ->groupBy('budget_range')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => $stats,
                'top_services' => $topServices,
                'budget_distribution' => $budgetDistribution
            ]
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contact_messages,id',
            'action' => 'required|in:mark_read,mark_replied,mark_closed,delete',
            'admin_notes' => 'nullable|string'
        ]);

        $messages = ContactMessage::whereIn('id', $validated['ids']);

        switch ($validated['action']) {
            case 'mark_read':
                $messages->update(['status' => ContactMessage::STATUS_READ]);
                $message = 'Messages marked as read';
                break;
            case 'mark_replied':
                $messages->update([
                    'status' => ContactMessage::STATUS_REPLIED,
                    'replied_at' => now()
                ]);
                $message = 'Messages marked as replied';
                break;
            case 'mark_closed':
                $messages->update(['status' => ContactMessage::STATUS_CLOSED]);
                $message = 'Messages marked as closed';
                break;
            case 'delete':
                $messages->delete();
                $message = 'Messages deleted successfully';
                break;
        }

        // Add admin notes if provided and not deleting
        if (isset($validated['admin_notes']) && $validated['action'] !== 'delete') {
            $messages->update(['admin_notes' => $validated['admin_notes']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }

    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:csv,json',
            'status' => 'nullable|in:new,read,replied,closed',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date'
        ]);

        $query = ContactMessage::query();

        // Apply filters
        if ($request->status) {
            $query->byStatus($request->status);
        }

        if ($request->from_date) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        $messages = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages,
            'count' => $messages->count(),
            'export_format' => $request->format
        ]);
    }
}
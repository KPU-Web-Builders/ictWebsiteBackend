
-- Site settings (logo, company info, etc.)
CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    setting_type ENUM('text', 'image', 'json', 'boolean') DEFAULT 'text',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);





-- =====================================================
-- 3. HOSTING PLANS & SERVICES
-- =====================================================

-- Service categories (Hosting, Domain, Development, etc.)
CREATE TABLE service_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Hosting plans
CREATE TABLE hosting_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    logo_url VARCHAR(255),
    category_id INT NOT NULL,
    monthly_price DECIMAL(10,2) NOT NULL,
    yearly_price DECIMAL(10,2) NOT NULL,
    monthly_renewal_price DECIMAL(10,2) NOT NULL,
    yearly_renewal_price DECIMAL(10,2) NOT NULL,
    is_highlighted BOOLEAN DEFAULT FALSE,
    is_popular BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE RESTRICT
);

-- Plan features
CREATE TABLE plan_features (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plan_id INT NOT NULL,
    feature_name VARCHAR(200) NOT NULL,
    is_included BOOLEAN NOT NULL,
    feature_value VARCHAR(100), -- e.g., "100 GB", "Unlimited", etc.
    tooltip TEXT,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (plan_id) REFERENCES hosting_plans(id) ON DELETE CASCADE
);

-- =====================================================
-- 4. SERVICES & PORTFOLIO
-- =====================================================

-- Services (CCTV, Network Design, Development, etc.)
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    short_description TEXT,
    full_description LONGTEXT,
    featured_image VARCHAR(255),
    gallery_images JSON, -- Store array of image URLs
    category_id INT NOT NULL,
    price_starting DECIMAL(10,2),
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE RESTRICT
);

-- Portfolio/Projects
CREATE TABLE portfolio (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    client_name VARCHAR(100),
    project_url VARCHAR(255),
    featured_image VARCHAR(255),
    gallery_images JSON,
    service_id INT,
    technologies_used JSON, -- Array of technologies
    project_date DATE,
    is_featured BOOLEAN DEFAULT FALSE,
    is_published BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);

-- =====================================================
-- 5. TEAM & CAREERS
-- =====================================================

-- Team members
CREATE TABLE team_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100) NOT NULL,
    bio TEXT,
    photo_url VARCHAR(255),
    is_verified BOOLEAN DEFAULT FALSE,
    skills JSON, -- Array of skills
    linkedin_url VARCHAR(255),
    github_url VARCHAR(255),
    twitter_url VARCHAR(255),
    email VARCHAR(100),
    phone VARCHAR(20),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    joined_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -- Job positions
-- CREATE TABLE job_positions (
--     id INT PRIMARY KEY AUTO_INCREMENT,
--     title VARCHAR(200) NOT NULL,
--     department VARCHAR(100),
--     location VARCHAR(100),
--     employment_type ENUM('full_time', 'part_time', 'contract', 'internship') DEFAULT 'full_time',
--     experience_level ENUM('entry', 'mid', 'senior', 'lead') DEFAULT 'mid',
--     salary_min DECIMAL(10,2),
--     salary_max DECIMAL(10,2),
--     currency VARCHAR(10) DEFAULT 'USD',
--     description LONGTEXT,
--     requirements LONGTEXT,
--     benefits TEXT,
--     is_active BOOLEAN DEFAULT TRUE,
--     posted_date DATE DEFAULT (CURDATE()),
--     closing_date DATE,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );


-- =====================================================
-- 6. FAQ SYSTEM
-- =====================================================

-- FAQ categories
CREATE TABLE faq_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- FAQ items
CREATE TABLE faqs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    question TEXT NOT NULL,
    answer LONGTEXT NOT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES faq_categories(id) ON DELETE SET NULL
);

-- =====================================================
-- 7. CONTACT & COMMUNICATION
-- =====================================================

-- Contact messages
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    company VARCHAR(100),
    subject VARCHAR(200),
    message LONGTEXT NOT NULL,
    service_interest VARCHAR(100), -- Which service they're interested in
    budget_range VARCHAR(50),
    preferred_contact ENUM('email', 'phone', 'both') DEFAULT 'email',
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    admin_notes TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    replied_at TIMESTAMP NULL
);


-- =====================================================
-- 8. TESTIMONIALS & REVIEWS
-- =====================================================

-- Client testimonials
CREATE TABLE testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(100) NOT NULL,
    company VARCHAR(100),
    position VARCHAR(100),
    testimonial TEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    photo_url VARCHAR(255),
    service_id INT,
    is_featured BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);

-- =====================================================
-- 9. BLOG SYSTEM
-- =====================================================

-- Blog categories
-- CREATE TABLE blog_categories (
--     id INT PRIMARY KEY AUTO_INCREMENT,
--     name VARCHAR(100) NOT NULL,
--     slug VARCHAR(100) UNIQUE NOT NULL,
--     description TEXT,
--     color VARCHAR(7), -- Hex color code
--     sort_order INT DEFAULT 0,
--     is_active BOOLEAN DEFAULT TRUE,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- Blog posts
-- CREATE TABLE blog_posts (
--     id INT PRIMARY KEY AUTO_INCREMENT,
--     title VARCHAR(200) NOT NULL,
--     slug VARCHAR(200) UNIQUE NOT NULL,
--     excerpt TEXT,
--     content LONGTEXT,
--     featured_image VARCHAR(255),
--     category_id INT,
--     author_id INT NOT NULL,
--     meta_description TEXT,
--     meta_keywords TEXT,
--     tags JSON, -- Array of tags
--     is_published BOOLEAN DEFAULT FALSE,
--     is_featured BOOLEAN DEFAULT FALSE,
--     views_count INT DEFAULT 0,
--     published_at TIMESTAMP NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--     FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
--     FOREIGN KEY (author_id) REFERENCES admins(id) ON DELETE RESTRICT
-- );



-- =====================================================
-- 11. ANALYTICS & TRACKING
-- =====================================================

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- Essential indexes
CREATE INDEX idx_hosting_plans_active ON hosting_plans(is_active, sort_order);
CREATE INDEX idx_services_category ON services(category_id, is_active);
CREATE INDEX idx_faqs_category ON faqs(category_id, is_active);
CREATE INDEX idx_blog_posts_published ON blog_posts(is_published, published_at DESC);
CREATE INDEX idx_orders_status ON orders(status, created_at DESC);
CREATE INDEX idx_contact_messages_status ON contact_messages(status, created_at DESC);
CREATE INDEX idx_analytics_date ON analytics_page_views(viewed_at);
CREATE INDEX idx_team_members_active ON team_members(is_active, sort_order);

-- =====================================================
-- INITIAL DATA SETUP
-- =====================================================

-- Insert default admin user (password: admin123 - change this!)
INSERT INTO admins (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@ictwebsite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'super_admin');

-- Insert service categories
INSERT INTO service_categories (name, slug, description, sort_order) VALUES
('Web Hosting', 'web-hosting', 'Web hosting services and plans', 1),
('Domain Services', 'domain-services', 'Domain registration and management', 2),
('Development', 'development', 'Web and software development services', 3),
('Network Solutions', 'network-solutions', 'Network design and setup services', 4),
('Security Systems', 'security-systems', 'CCTV and security solutions', 5),
('IT Consulting', 'it-consulting', 'IT consultation and support services', 6);

-- Insert FAQ categories
INSERT INTO faq_categories (name, description, sort_order) VALUES
('General', 'General questions about our services', 1),
('Hosting', 'Web hosting related questions', 2),
('Domain', 'Domain registration questions', 3),
('Billing', 'Payment and billing questions', 4),
('Technical', 'Technical support questions', 5);

-- Insert blog categories
INSERT INTO blog_categories (name, slug, description, color) VALUES
('Web Development', 'web-development', 'Articles about web development', '#3B82F6'),
('Hosting Tips', 'hosting-tips', 'Tips and guides for web hosting', '#10B981'),
('Technology News', 'tech-news', 'Latest technology news and updates', '#F59E0B'),
('Security', 'security', 'Cybersecurity and website security', '#EF4444'),
('Business', 'business', 'Business and entrepreneurship content', '#8B5CF6');

-- Insert site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'ICT Solutions', 'text', 'Website name'),
('site_tagline', 'Your Trusted ICT Partner', 'text', 'Website tagline'),
('company_email', 'info@ictwebsite.com', 'text', 'Company email address'),
('company_phone', '+93-XX-XXX-XXXX', 'text', 'Company phone number'),
('company_address', 'Kabul, Afghanistan', 'text', 'Company address'),
('facebook_url', 'https://facebook.com/ictwebsite', 'text', 'Facebook page URL'),
('twitter_url', 'https://twitter.com/ictwebsite', 'text', 'Twitter profile URL'),
('linkedin_url', 'https://linkedin.com/company/ictwebsite', 'text', 'LinkedIn company page'),
('maintenance_mode', 'false', 'boolean', 'Website maintenance mode');

-- =====================================================
-- DATABASE SETUP COMPLETE
-- =====================================================

-- Create a view for easy access to plan data with features
CREATE VIEW hosting_plans_with_features AS
SELECT 
    hp.*,
    sc.name as category_name,
    JSON_ARRAYAGG(
        JSON_OBJECT(
            'feature_name', pf.feature_name,
            'is_included', pf.is_included,
            'feature_value', pf.feature_value,
            'tooltip', pf.tooltip
        )
    ) as features
FROM hosting_plans hp
LEFT JOIN service_categories sc ON hp.category_id = sc.id
LEFT JOIN plan_features pf ON hp.id = pf.plan_id
WHERE hp.is_active = TRUE
GROUP BY hp.id
ORDER BY hp.sort_order;

COMMIT;
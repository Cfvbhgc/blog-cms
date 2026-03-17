-- Blog CMS Database Schema
-- MySQL 8.0

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(280) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt VARCHAR(500),
    seo_title VARCHAR(255),
    seo_description VARCHAR(320),
    category_id INT,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_slug (slug),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_tags (
    article_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    author_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    INDEX idx_article (article_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed: Categories
INSERT INTO categories (name, slug, description) VALUES
('Programming', 'programming', 'Articles about programming languages, paradigms, and best practices'),
('Web Development', 'web-development', 'Frontend and backend web development tutorials and guides'),
('DevOps', 'devops', 'CI/CD, Docker, Kubernetes, and infrastructure automation'),
('Databases', 'databases', 'SQL, NoSQL, database design and optimization'),
('Career', 'career', 'Career advice, interviews, and professional growth in tech');

-- Seed: Tags
INSERT INTO tags (name, slug) VALUES
('PHP', 'php'),
('MySQL', 'mysql'),
('Docker', 'docker'),
('JavaScript', 'javascript'),
('REST API', 'rest-api'),
('MVC', 'mvc'),
('Security', 'security'),
('Performance', 'performance'),
('Testing', 'testing'),
('Linux', 'linux');

-- Seed: Articles
INSERT INTO articles (title, slug, content, excerpt, seo_title, seo_description, category_id, status) VALUES
('Getting Started with PHP 8.2', 'getting-started-with-php-82',
 '<p>PHP 8.2 brings exciting new features including readonly classes, DNF types, and standalone types for null, true, and false. In this comprehensive guide, we will explore each feature with practical examples.</p><h2>Readonly Classes</h2><p>Readonly classes make all declared properties readonly by default. This is particularly useful for DTO (Data Transfer Object) patterns.</p><pre><code>readonly class UserDTO {\n    public function __construct(\n        public string $name,\n        public string $email,\n        public int $age\n    ) {}\n}</code></pre><p>This eliminates the need to mark each property individually as readonly, resulting in cleaner and more maintainable code.</p><h2>DNF Types</h2><p>Disjunctive Normal Form types allow combining union and intersection types. For example:</p><pre><code>function process((Countable&Iterator)|null $input): void {\n    // ...\n}</code></pre><p>This powerful feature enables more precise type declarations in your code.</p>',
 'Explore the new features in PHP 8.2 including readonly classes, DNF types, and more.',
 'Getting Started with PHP 8.2 - New Features Guide', 'Learn about PHP 8.2 new features: readonly classes, DNF types, standalone types with practical examples.',
 1, 'published'),

('Building RESTful APIs with Pure PHP', 'building-restful-apis-pure-php',
 '<p>While frameworks like Laravel and Symfony make API development convenient, understanding how to build REST APIs with pure PHP teaches fundamental concepts that every developer should know.</p><h2>Routing</h2><p>The foundation of any API is its routing system. We can parse the REQUEST_URI and REQUEST_METHOD to route requests:</p><pre><code>$method = $_SERVER[\"REQUEST_METHOD\"];\n$uri = parse_url($_SERVER[\"REQUEST_URI\"], PHP_URL_PATH);\n$segments = explode(\"/\", trim($uri, \"/\"));</code></pre><h2>Response Handling</h2><p>Proper HTTP responses are crucial for REST APIs. Always set appropriate status codes and headers:</p><pre><code>header(\"Content-Type: application/json\");\nhttp_response_code(200);\necho json_encode([\"data\" => $result]);</code></pre><p>Remember to handle errors gracefully and return meaningful error messages with appropriate HTTP status codes.</p>',
 'Learn to build REST APIs from scratch using pure PHP without any framework.',
 'Building RESTful APIs with Pure PHP', 'Step-by-step guide to creating RESTful APIs using pure PHP, covering routing, controllers, and response handling.',
 1, 'published'),

('Docker for PHP Developers', 'docker-for-php-developers',
 '<p>Docker revolutionizes how PHP developers set up and manage development environments. No more \"works on my machine\" problems!</p><h2>Why Docker?</h2><p>Docker provides consistent environments across development, staging, and production. Every team member works with identical PHP versions, extensions, and configurations.</p><h2>Basic Setup</h2><p>A typical PHP Docker setup includes three services: Nginx (web server), PHP-FPM (application), and MySQL (database).</p><pre><code>version: \"3.8\"\nservices:\n  nginx:\n    image: nginx:alpine\n    ports:\n      - \"8080:80\"\n  php:\n    build: .\n    volumes:\n      - ./:/var/www/html\n  mysql:\n    image: mysql:8.0</code></pre><p>This simple configuration gets you a complete development environment in minutes.</p>',
 'Set up a complete PHP development environment using Docker containers.',
 'Docker for PHP Developers - Complete Guide', 'Learn how to containerize PHP applications with Docker, including Nginx, PHP-FPM, and MySQL setup.',
 3, 'published'),

('MySQL Query Optimization Techniques', 'mysql-query-optimization',
 '<p>Slow database queries are one of the most common performance bottlenecks in web applications. Understanding how to optimize MySQL queries is an essential skill.</p><h2>Using EXPLAIN</h2><p>The EXPLAIN statement is your best friend for query optimization. It shows how MySQL executes a query:</p><pre><code>EXPLAIN SELECT a.*, c.name FROM articles a\nJOIN categories c ON a.category_id = c.id\nWHERE a.status = \"published\"\nORDER BY a.created_at DESC;</code></pre><h2>Indexing Strategies</h2><p>Proper indexing can dramatically improve query performance. Focus on columns used in WHERE, JOIN, and ORDER BY clauses.</p><p>Composite indexes should follow the left-prefix rule and consider the cardinality of columns.</p>',
 'Learn essential MySQL query optimization techniques for better performance.',
 'MySQL Query Optimization Techniques', 'Master MySQL query optimization with EXPLAIN analysis, indexing strategies, and query rewriting techniques.',
 4, 'published'),

('Understanding MVC Architecture', 'understanding-mvc-architecture',
 '<p>Model-View-Controller (MVC) is one of the most widely used architectural patterns in web development. Understanding MVC deeply will make you a better developer.</p><h2>The Three Components</h2><p><strong>Model</strong> represents your data and business logic. It communicates with the database and enforces business rules.</p><p><strong>View</strong> is responsible for presenting data to the user. It should contain minimal logic - only display logic.</p><p><strong>Controller</strong> acts as the intermediary between Model and View. It processes requests, interacts with models, and selects views.</p><h2>Benefits</h2><ul><li>Separation of concerns</li><li>Code reusability</li><li>Easier testing</li><li>Better team collaboration</li></ul>',
 'Deep dive into the MVC architectural pattern and its implementation.',
 'Understanding MVC Architecture in PHP', 'Comprehensive guide to MVC architecture: models, views, controllers, and how they work together in PHP applications.',
 1, 'published'),

('Introduction to JavaScript ES2024', 'introduction-javascript-es2024',
 '<p>JavaScript continues to evolve with new features that make development more efficient and enjoyable. Let us explore the latest additions in ES2024.</p><h2>Array Grouping</h2><p>The new Object.groupBy() and Map.groupBy() methods simplify data grouping operations:</p><pre><code>const items = [\n  { type: \"fruit\", name: \"apple\" },\n  { type: \"vegetable\", name: \"carrot\" },\n  { type: \"fruit\", name: \"banana\" }\n];\nconst grouped = Object.groupBy(items, item => item.type);</code></pre><p>This is a welcome addition that reduces boilerplate code significantly.</p>',
 'Explore the newest JavaScript features introduced in ES2024.',
 'Introduction to JavaScript ES2024 Features', 'Discover JavaScript ES2024 new features including array grouping, promise improvements, and more.',
 2, 'published'),

('Web Security Best Practices', 'web-security-best-practices',
 '<p>Security should never be an afterthought. As a web developer, understanding common vulnerabilities and how to prevent them is crucial.</p><h2>SQL Injection</h2><p>Always use prepared statements:</p><pre><code>$stmt = $pdo->prepare(\"SELECT * FROM users WHERE email = :email\");\n$stmt->execute([\"email\" => $userInput]);</code></pre><h2>Cross-Site Scripting (XSS)</h2><p>Always escape output data:</p><pre><code>echo htmlspecialchars($userInput, ENT_QUOTES, \"UTF-8\");</code></pre><h2>CSRF Protection</h2><p>Implement CSRF tokens for all state-changing operations to prevent cross-site request forgery attacks.</p>',
 'Essential security practices every web developer must follow.',
 'Web Security Best Practices for Developers', 'Learn critical web security practices: preventing SQL injection, XSS, CSRF, and securing your applications.',
 2, 'published'),

('Getting Started with Unit Testing in PHP', 'unit-testing-php',
 '<p>Testing is a crucial part of professional software development. PHPUnit is the de facto standard for testing PHP applications.</p><h2>Installation</h2><pre><code>composer require --dev phpunit/phpunit</code></pre><h2>Writing Your First Test</h2><pre><code>class CalculatorTest extends TestCase {\n    public function testAddition(): void {\n        $calc = new Calculator();\n        $this->assertEquals(4, $calc->add(2, 2));\n    }\n}</code></pre><p>Follow the AAA pattern: Arrange, Act, Assert. This keeps your tests clean and readable.</p>',
 'Learn the fundamentals of unit testing PHP applications with PHPUnit.',
 'Unit Testing in PHP with PHPUnit', 'Getting started with PHPUnit: installation, writing tests, assertions, and test-driven development in PHP.',
 1, 'draft'),

('Linux Command Line Essentials', 'linux-command-line-essentials',
 '<p>The command line is a powerful tool that every developer should master. Here are the essential Linux commands you need to know.</p><h2>File Operations</h2><pre><code>ls -la     # List all files with details\ncp -r src/ dest/  # Copy directory recursively\nfind . -name \"*.php\" -type f  # Find PHP files</code></pre><h2>Text Processing</h2><pre><code>grep -r \"function\" --include=\"*.php\" .  # Search in PHP files\nawk \"{print $1}\" file.txt  # Print first column\nsed \"s/old/new/g\" file.txt  # Replace text</code></pre>',
 'Master essential Linux command line tools for everyday development.',
 'Linux Command Line Essentials for Developers', 'Essential Linux commands every developer should know: file operations, text processing, system management.',
 3, 'published'),

('Building a Career in Tech', 'building-career-in-tech',
 '<p>The tech industry offers incredible opportunities, but navigating your career path can be challenging. Here are strategies for building a successful tech career.</p><h2>Continuous Learning</h2><p>Technology evolves rapidly. Dedicate time each week to learning new tools, languages, and concepts. Build side projects to apply what you learn.</p><h2>Open Source Contribution</h2><p>Contributing to open source projects demonstrates your skills, helps you learn from experienced developers, and builds your professional network.</p><h2>Technical Interviews</h2><p>Practice data structures and algorithms regularly. Understand system design concepts. Most importantly, communicate your thought process clearly during interviews.</p>',
 'Strategies and advice for building a successful career in tech.',
 'Building a Successful Career in Tech', 'Career advice for developers: continuous learning, open source, networking, and interview preparation tips.',
 5, 'published');

-- Seed: Article-Tag associations
INSERT INTO article_tags (article_id, tag_id) VALUES
(1, 1), (1, 6),
(2, 1), (2, 5),
(3, 3), (3, 1), (3, 10),
(4, 2), (4, 8),
(5, 6), (5, 1),
(6, 4),
(7, 7), (7, 1),
(8, 1), (8, 9),
(9, 10),
(10, 1);

-- Seed: Comments
INSERT INTO comments (article_id, author_name, email, content, status) VALUES
(1, 'Alex Developer', 'alex@example.com', 'Great overview of PHP 8.2 features! The readonly classes are a game changer.', 'approved'),
(1, 'Maria Chen', 'maria@example.com', 'Would love to see more examples of DNF types in real-world applications.', 'approved'),
(2, 'John Smith', 'john@example.com', 'This is exactly what I needed. Building APIs without a framework really helped me understand the fundamentals.', 'approved'),
(3, 'Sarah K', 'sarah@example.com', 'Docker has completely changed my development workflow. Great article!', 'approved'),
(3, 'DevOps Mike', 'mike@example.com', 'You should also cover multi-stage builds for production optimization.', 'pending'),
(5, 'Junior Dev', 'junior@example.com', 'Finally I understand MVC! Thanks for the clear explanation.', 'approved'),
(7, 'Security Pro', 'secpro@example.com', 'Good basics, but you should also mention Content Security Policy headers.', 'pending'),
(4, 'DBA Expert', 'dba@example.com', 'The EXPLAIN section is very well written. Maybe add a section about query caching?', 'approved');

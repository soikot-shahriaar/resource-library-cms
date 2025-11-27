-- Sample Data for Resource Library CMS
-- Run this after setup.sql to populate the database with test data

USE resource_library_cms;

-- Insert additional test users
INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES
('john_doe', 'john@example.com', '$2y$10$Fu.bstJ/GCC5Tg1/nuBSYehQN0LC5KPijMDoMwT0rCkjOclD48vHK', 'John', 'Doe'),
('jane_smith', 'jane@example.com', '$2y$10$.AfSgJnoA760L4CAGeQyHO8zJf9wC7m2rIohZRjGQpQmdvBPz.Woi', 'Jane', 'Smith'),
('mike_wilson', 'mike@example.com', '$2y$10$hGvSzkOYcfe5O89RbwMOZOZxU8kqThXO/u8tEaBae90oTOwtEmPo6', 'Mike', 'Wilson');

-- Insert sample resources
INSERT INTO resources (title, description, resource_type, external_url, category_id, user_id) VALUES
('PHP Official Documentation', 'Complete PHP documentation and reference guide', 'link', 'https://www.php.net/docs.php', 1, 1),
('MySQL Tutorial', 'Comprehensive MySQL database tutorial for beginners', 'tutorial', 'https://www.mysqltutorial.org/', 4, 1),
('Web Development Best Practices', 'A guide to modern web development practices and standards', 'link', 'https://developer.mozilla.org/en-US/docs/Web', 1, 2),
('Introduction to Database Design', 'Learn the fundamentals of database design and normalization', 'tutorial', 'https://www.studytonight.com/dbms/', 4, 2),
('CSS Grid Layout Guide', 'Complete guide to CSS Grid layout system', 'link', 'https://css-tricks.com/snippets/css/complete-guide-grid/', 1, 3),
('JavaScript ES6 Features', 'Overview of new features in ECMAScript 6', 'tutorial', 'https://es6-features.org/', 4, 3),
('React.js Documentation', 'Official React.js documentation and tutorials', 'link', 'https://reactjs.org/docs/', 1, 1),
('Node.js Getting Started', 'Introduction to Node.js for server-side development', 'tutorial', 'https://nodejs.org/en/docs/guides/getting-started-guide/', 4, 2),
('Bootstrap Framework', 'Popular CSS framework for responsive web design', 'link', 'https://getbootstrap.com/docs/', 1, 3),
('Git Version Control', 'Learn Git for version control and collaboration', 'tutorial', 'https://git-scm.com/doc', 4, 1),
('Visual Studio Code', 'Popular code editor with extensive features', 'link', 'https://code.visualstudio.com/docs', 5, 2),
('Docker Documentation', 'Containerization platform documentation', 'link', 'https://docs.docker.com/', 5, 3),
('Machine Learning Basics', 'Introduction to machine learning concepts', 'tutorial', 'https://www.coursera.org/learn/machine-learning', 6, 1),
('Data Science with Python', 'Python libraries and tools for data science', 'tutorial', 'https://www.datacamp.com/courses/intro-to-python-for-data-science', 6, 2),
('API Design Best Practices', 'Guidelines for designing RESTful APIs', 'link', 'https://restfulapi.net/', 1, 3);

-- Update timestamps to show variety
UPDATE resources SET created_at = DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 30) DAY) WHERE id BETWEEN 1 AND 15;
UPDATE resources SET updated_at = created_at WHERE id BETWEEN 1 AND 15;


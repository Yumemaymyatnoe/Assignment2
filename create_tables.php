<?php
// File: create_tables.php
require_once 'settings.php';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    
    // Create EOI table
    $eoi_table_sql = "
    CREATE TABLE IF NOT EXISTS eoi (
        EOInumber INT AUTO_INCREMENT PRIMARY KEY,
        job_ref VARCHAR(20) NOT NULL,
        first_name VARCHAR(20) NOT NULL,
        last_name VARCHAR(20) NOT NULL,
        street_address VARCHAR(40) NOT NULL,
        suburb_town VARCHAR(40) NOT NULL,
        state VARCHAR(20) NOT NULL,
        postcode VARCHAR(4) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(12) NOT NULL,
        skill1 VARCHAR(20),
        skill2 VARCHAR(20),
        skill3 VARCHAR(20),
        skill4 VARCHAR(20),
        other_skills TEXT,
        status ENUM('New', 'Current', 'Final') DEFAULT 'New',
        application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($eoi_table_sql);
    
    // Create jobs table
    $jobs_table_sql = "
    CREATE TABLE IF NOT EXISTS jobs (
        job_id INT AUTO_INCREMENT PRIMARY KEY,
        job_ref VARCHAR(20) UNIQUE NOT NULL,
        title VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        requirements TEXT NOT NULL,
        salary_range VARCHAR(50),
        location VARCHAR(50),
        posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($jobs_table_sql);
    
    $sample_jobs = [
        [
            'job_ref' => 'B008',  
            'title' => 'Software Programmer',  
            'description' => 'Join our Programming team to create innovative solutions. You will work on various projects including web applications, mobile apps, and enterprise software.',
            'requirements' => 'Python, Java, C++, Database Design',
            'salary_range' => '$75,000 - $95,000',
            'location' => 'Sydney, NSW'
        ],
        [
            'job_ref' => 'C642',  
            'title' => 'Web Developer', 
            'description' => 'We are looking for a skilled Web Developer to join our dynamic team. You will be responsible for developing and maintaining web applications using modern technologies.',
            'requirements' => 'PHP, HTML, CSS, JavaScript, MySQL',
            'salary_range' => '$70,000 - $90,000',
            'location' => 'Melbourne, VIC'
        ]
    ];
    
    $insert_job = $pdo->prepare("INSERT IGNORE INTO jobs (job_ref, title, description, requirements, salary_range, location) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($sample_jobs as $job) {
        $insert_job->execute([
            $job['job_ref'],    // B008, C642
            $job['title'],      // Software Programmer, Web Developer
            $job['description'],
            $job['requirements'],
            $job['salary_range'],
            $job['location']
        ]);
    }
    
    // Set page title
    $page_title = "Database Setup Complete - TechNova IT Careers";
    
    // Include header
    include 'header.inc';
    
    echo "
        <div class='setup-container'>
            <div class='setup-card'>
                <div class='setup-icon'>
                    <i class='fas fa-database'></i>
                </div>
                <h1>Database Setup Complete</h1>
                <p>Database and tables created successfully with sample data!</p>
                <p><strong>Job Reference Numbers Created:</strong></p>
                <ul style='text-align: left; margin: 1rem 0;'>
                    <li><strong>B008</strong> - Software Programmer</li>
                    <li><strong>C642</strong> - Web Developer</li>
                </ul>
                <a href='apply.php' class='setup-button'>
                    <i class='fas fa-arrow-right'></i>
                    Go to Application Form
                </a>
            </div>
        </div>";
    
    // Include footer
    include 'footer.inc';
    
} catch (PDOException $e) {
    // Set page title for error page
    $page_title = "Database Setup Error - TechNova IT Careers";
    
    // Include header
    include 'header.inc';
    
    echo "
        <div class='setup-container'>
            <div class='setup-card'>
                <div class='setup-icon'>
                    <i class='fas fa-exclamation-triangle'></i>
                </div>
                <h1>Database Setup Error</h1>
                <p>Error: " . htmlspecialchars($e->getMessage()) . "</p>
                <a href='apply.php' class='setup-button'>
                    <i class='fas fa-arrow-right'></i>
                    Go to Application Form
                </a>
            </div>
        </div>";
    
    // Include footer
    include 'footer.inc';
}
?>
<?php
// File: process_eoi.php

// Prevent direct access
if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST['jobRef'])) {
    header("Location: apply.php");
    exit();
}

// Include settings
require_once 'settings.php';

// Validation functions
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validate_name($name) {
    return preg_match("/^[A-Za-z\s'-]{1,20}$/", $name) && strlen($name) >= 1;
}

function validate_postcode_state($postcode, $state) {
    $state_first_digits = [
        'VIC' => ['3', '8'],
        'NSW' => ['1', '2'],
        'QLD' => ['4', '9'],
        'WA' => ['6'],
        'SA' => ['5'],
        'TAS' => ['7'],
        'ACT' => ['0'],
        'NT' => ['0']
    ];
    
    if (strlen($postcode) !== 4 || !ctype_digit($postcode)) {
        return false;
    }
    
    $first_digit = substr($postcode, 0, 1);
    return isset($state_first_digits[$state]) && in_array($first_digit, $state_first_digits[$state]);
}

function validate_dob($dob) {
    if (!preg_match("/^\d{2}\/\d{2}\/\d{4}$/", $dob)) {
        return false;
    }
    
    list($day, $month, $year) = explode('/', $dob);
    
    if (!checkdate($month, $day, $year)) {
        return false;
    }
    
    // Check if age is reasonable (between 16 and 100 years)
    $birth_date = DateTime::createFromFormat('d/m/Y', $dob);
    $today = new DateTime();
    $age = $today->diff($birth_date)->y;
    
    return $age >= 16 && $age <= 100;
}

function validate_phone($phone) {
    $clean_phone = preg_replace('/\s+/', '', $phone);
    return preg_match("/^\d{8,12}$/", $clean_phone);
}

// Initialize variables
$errors = [];
$field_errors = [];
$form_data = [];

// Sanitize all POST data
foreach ($_POST as $key => $value) {
    $form_data[$key] = sanitize_input($value);
}

// Extract form data
$job_ref = $form_data['jobRef'] ?? '';
$first_name = $form_data['firstName'] ?? '';
$last_name = $form_data['lastName'] ?? '';
$dob = $form_data['dob'] ?? '';
$gender = $form_data['gender'] ?? '';
$street = $form_data['street'] ?? '';
$suburb = $form_data['suburb'] ?? '';
$state = $form_data['state'] ?? '';
$postcode = $form_data['postcode'] ?? '';
$email = $form_data['email'] ?? '';
$phone = $form_data['phone'] ?? '';
$skills = $form_data['skills'] ?? [];
$other_skills = $form_data['otherSkills'] ?? '';

// Server-side validation

// Job Reference numbers
$valid_job_refs = ['B008', 'C642']; // Specific reference numbers: B008=Software Programmer, C642=Web Developer
if (!in_array($job_ref, $valid_job_refs)) {
    $errors[] = "Please select a valid job position.";
    $field_errors['job_ref'] = "Invalid job selection";
}

// First Name
if (!validate_name($first_name)) {
    $errors[] = "First name must contain only letters and be maximum 20 characters.";
    $field_errors['first_name'] = "Invalid first name format";
}

// Last Name
if (!validate_name($last_name)) {
    $errors[] = "Last name must contain only letters and be maximum 20 characters.";
    $field_errors['last_name'] = "Invalid last name format";
}

// Date of Birth
if (!validate_dob($dob)) {
    $errors[] = "Please enter a valid date of birth in dd/mm/yyyy format (age 16-100).";
    $field_errors['dob'] = "Invalid date format or age";
}

// Gender
$valid_genders = ['Male', 'Female', 'Other', 'Prefer not to say'];
if (!in_array($gender, $valid_genders)) {
    $errors[] = "Please select your gender.";
    $field_errors['gender'] = "Gender selection required";
}

// Street Address
if (empty($street) || strlen($street) > 40) {
    $errors[] = "Street address is required and must be maximum 40 characters.";
    $field_errors['street'] = "Address required (max 40 chars)";
}

// Suburb/Town
if (empty($suburb) || strlen($suburb) > 40) {
    $errors[] = "Suburb/town is required and must be maximum 40 characters.";
    $field_errors['suburb'] = "Suburb required (max 40 chars)";
}

// State
$valid_states = ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'];
if (!in_array($state, $valid_states)) {
    $errors[] = "Please select a valid Australian state.";
    $field_errors['state'] = "State selection required";
}

// Postcode
if (!validate_postcode_state($postcode, $state)) {
    $errors[] = "Invalid postcode for $state. Postcodes must match the selected state.";
    $field_errors['postcode'] = "Postcode doesn't match state";
}

// Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
    $field_errors['email'] = "Invalid email format";
}

// Phone
if (!validate_phone($phone)) {
    $errors[] = "Phone number must contain 8 to 12 digits (spaces allowed).";
    $field_errors['phone'] = "Invalid phone number format";
}

// Skills
$valid_skills = ['HTML', 'CSS', 'JavaScript', 'Python', 'PHP', 'MySQL', 'Java', 'React', 'Node.js', 'Git', 'AWS', 'Docker'];
if (empty($skills)) {
    $errors[] = "Please select at least one technical skill.";
    $field_errors['skills'] = "At least one skill required";
} else {
    foreach ($skills as $skill) {
        if (!in_array($skill, $valid_skills)) {
            $errors[] = "Invalid skill selected.";
            $field_errors['skills'] = "Invalid skill selection";
            break;
        }
    }
}

// If there are errors, redirect back to form with error messages
if (!empty($errors)) {
    $error_query = http_build_query([
        'errors' => json_encode($field_errors),
        'field_data' => json_encode($form_data)
    ]);
    header("Location: apply.php?$error_query");
    exit();
}

// Process application if no errors
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if EOI table exists, create if not
    $table_check = $pdo->query("SHOW TABLES LIKE 'eoi'");
    if ($table_check->rowCount() == 0) {
        $create_table_sql = "
        CREATE TABLE eoi (
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
        $pdo->exec($create_table_sql);
    }
    
    // Prepare skills
    $skill1 = $skill2 = $skill3 = $skill4 = null;
    if (isset($skills[0])) $skill1 = $skills[0];
    if (isset($skills[1])) $skill2 = $skills[1];
    if (isset($skills[2])) $skill3 = $skills[2];
    if (isset($skills[3])) $skill4 = $skills[3];
    
    // Clean phone number
    $clean_phone = preg_replace('/\s+/', '', $phone);
    
    // Insert application using prepared statements
    $sql = "INSERT INTO eoi (job_ref, first_name, last_name, street_address, suburb_town, state, postcode, email, phone, skill1, skill2, skill3, skill4, other_skills) 
            VALUES (:job_ref, :first_name, :last_name, :street_address, :suburb_town, :state, :postcode, :email, :phone, :skill1, :skill2, :skill3, :skill4, :other_skills)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':job_ref' => $job_ref,
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':street_address' => $street,
        ':suburb_town' => $suburb,
        ':state' => $state,
        ':postcode' => $postcode,
        ':email' => $email,
        ':phone' => $clean_phone,
        ':skill1' => $skill1,
        ':skill2' => $skill2,
        ':skill3' => $skill3,
        ':skill4' => $skill4,
        ':other_skills' => $other_skills
    ]);
    
    $eoi_number = $pdo->lastInsertId();
    $success = true;
    
} catch (PDOException $e) {
    $errors[] = "We encountered a system error. Please try again later.";
    $error_query = http_build_query([
        'errors' => json_encode(['system' => 'Database error occurred']),
        'field_data' => json_encode($form_data)
    ]);
    header("Location: apply.php?$error_query");
    exit();
}

// Set page title for success page
$page_title = "Application Successful - TechNova IT Careers";

// Include header for success page
include 'header.inc.php';
?>

        <div class="success-container">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="success-title">Application Submitted Successfully!</h2>
                <p class="success-message">Thank you for your interest in joining TechNova, <strong><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></strong>!</p>
                
                <div class="eoi-display">
                    <div class="eoi-label">Your Application Reference</div>
                    <div class="eoi-number">#<?php echo $eoi_number; ?></div>
                    <p class="eoi-note">Please save this number for future reference</p>
                </div>

                <div class="application-summary">
                    <h3><i class="fas fa-clipboard-list"></i> Application Summary</h3>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="summary-label"><i class="fas fa-briefcase"></i> Position Applied:</span>
                            <span class="summary-value"><?php echo htmlspecialchars($job_ref); ?> - 
                                <?php 
                                // Display job title based on reference number
                                if ($job_ref == 'B008') {
                                    echo 'Software Programmer';
                                } elseif ($job_ref == 'C642') {
                                    echo 'Web Developer';
                                } else {
                                    echo htmlspecialchars($job_ref);
                                }
                                ?>
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label"><i class="fas fa-user"></i> Full Name:</span>
                            <span class="summary-value"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label"><i class="fas fa-envelope"></i> Email:</span>
                            <span class="summary-value"><?php echo htmlspecialchars($email); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label"><i class="fas fa-phone"></i> Phone:</span>
                            <span class="summary-value"><?php echo htmlspecialchars($phone); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label"><i class="fas fa-map-marker-alt"></i> Location:</span>
                            <span class="summary-value"><?php echo htmlspecialchars($suburb . ', ' . $state . ' ' . $postcode); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label"><i class="fas fa-calendar-alt"></i> Application Date:</span>
                            <span class="summary-value"><?php echo date('d/m/Y'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="next-steps">
                    <h3><i class="fas fa-road"></i> What Happens Next?</h3>
                    <div class="steps-timeline">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <strong>Application Review</strong>
                                <p>Our HR team will review your application within 3-5 business days</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <strong>Interview Invitation</strong>
                                <p>If shortlisted, we'll contact you to schedule an interview</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <strong>Final Decision</strong>
                                <p>We'll notify you of the outcome within 2 weeks of your interview</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="apply.php" class="action-button secondary">
                        <i class="fas fa-plus-circle"></i>
                        Submit Another Application
                    </a>
                    <a href="index.php" class="action-button primary">
                        <i class="fas fa-home"></i>
                        Return to Homepage
                    </a>
                </div>
            </div>
        </div>

<?php
// Include footer for success page
include 'footer.inc';
?>
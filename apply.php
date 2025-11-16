<?php
// File: apply.php
require_once 'settings.php';

// Set page title
$page_title = "Apply Now - TechNova IT Careers";

// Fetch available job references from database
$jobs = [];
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT job_ref, title FROM jobs");
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If database error, use default options with specific reference numbers
    $jobs = [
        ['job_ref' => 'B008', 'title' => 'Software Programmer'],
        ['job_ref' => 'C642', 'title' => 'Web Developer']
    ];
}

// Display form errors if redirected from process_eoi.php
$form_errors = [];
$field_data = [];
if (isset($_GET['errors']) && isset($_GET['field_data'])) {
    $form_errors = json_decode(urldecode($_GET['errors']), true);
    $field_data = json_decode(urldecode($_GET['field_data']), true);
}

// Include header
include 'header.inc';
?>

    <div class="form-container">
      <div class="form-header">
        <div class="header-content">
          <h2>Join Our Team</h2>
          <p class="form-subtitle">Start your journey with TechNova - Fill out the application form below</p>
          
          <?php if (!empty($form_errors)): ?>
            <div class="error-banner">
              <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
              <div class="error-content">
                <h3>Please correct the following errors:</h3>
                <ul>
                  <?php foreach ($form_errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <button class="error-close" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
              </button>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <form id="applicationForm" action="process_eoi.php" method="post" novalidate="novalidate" class="elegant-form">
        
        <!-- Personal Information Section -->
        <fieldset class="form-section">
          <legend class="section-legend">
            <i class="fas fa-user"></i>
            Personal Information
          </legend>
          
          <div class="form-row">
            <div class="form-group">
              <label for="jobRef" class="form-label">
                <i class="fas fa-briefcase"></i>
                Job Reference Number *
              </label>
              <div class="input-wrapper">
                <select id="jobRef" name="jobRef" class="form-select <?php echo (isset($form_errors['job_ref'])) ? 'error' : ''; ?>" required>
                  <option value="">Choose a position</option>
                  <?php foreach ($jobs as $job): ?>
                    <option value="<?php echo htmlspecialchars($job['job_ref']); ?>" 
                      <?php echo (isset($field_data['jobRef']) && $field_data['jobRef'] == $job['job_ref']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($job['job_ref']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php if (isset($form_errors['job_ref'])): ?>
                <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['job_ref']); ?></span>
              <?php endif; ?>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="firstName" class="form-label">
                <i class="fas fa-signature"></i>
                First Name *
              </label>
              <div class="input-wrapper">
                <input type="text" id="firstName" name="firstName" maxlength="20" 
                       value="<?php echo isset($field_data['firstName']) ? htmlspecialchars($field_data['firstName']) : ''; ?>"
                       class="form-input <?php echo (isset($form_errors['first_name'])) ? 'error' : ''; ?>" 
                       placeholder="Enter your first name" required>
                <i class="fas fa-user input-icon"></i>
              </div>
              <?php if (isset($form_errors['first_name'])): ?>
                <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['first_name']); ?></span>
              <?php endif; ?>
            </div>
            
            <div class="form-group">
              <label for="lastName" class="form-label">
                <i class="fas fa-signature"></i>
                Last Name *
              </label>
              <div class="input-wrapper">
                <input type="text" id="lastName" name="lastName" maxlength="20"
                       value="<?php echo isset($field_data['lastName']) ? htmlspecialchars($field_data['lastName']) : ''; ?>"
                       class="form-input <?php echo (isset($form_errors['last_name'])) ? 'error' : ''; ?>" 
                       placeholder="Enter your last name" required>
                <i class="fas fa-user input-icon"></i>
              </div>
              <?php if (isset($form_errors['last_name'])): ?>
                <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['last_name']); ?></span>
              <?php endif; ?>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="dob" class="form-label">
                <i class="fas fa-calendar-alt"></i>
                Date of Birth *
              </label>
              <div class="input-wrapper">
                <input type="text" id="dob" name="dob" 
                       value="<?php echo isset($field_data['dob']) ? htmlspecialchars($field_data['dob']) : ''; ?>"
                       class="form-input <?php echo (isset($form_errors['dob'])) ? 'error' : ''; ?>" 
                       placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" required>
                <i class="fas fa-birthday-cake input-icon"></i>
              </div>
              <?php if (isset($form_errors['dob'])): ?>
                <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['dob']); ?></span>
              <?php endif; ?>
            </div>
            
            <div class="form-group">
              <fieldset class="gender-fieldset">
                  <legend class="form-label">
                      <i class="fas fa-venus-mars"></i>
                      Gender *
                  </legend>
                  <div class="radio-group">
                      <?php 
                      $genders = [
                          'Male' => 'Male',
                          'Female' => 'Female', 
                          'Other' => 'Other',
                          'Prefer not to say' => 'Prefer not to say'
                      ];
                      $current_gender = $field_data['gender'] ?? '';
                      foreach ($genders as $value => $label): 
                      ?>
                          <label class="radio-option">
                              <input type="radio" name="gender" value="<?php echo $value; ?>" 
                                    <?php echo ($current_gender == $value) ? 'checked' : ''; ?> required>
                              <span class="radio-design">
                                  <i class="fas fa-check"></i>
                              </span>
                              <span class="radio-label"><?php echo $label; ?></span>
                          </label>
                      <?php endforeach; ?>
                  </div>
              </fieldset>
              <?php if (isset($form_errors['gender'])): ?>
                  <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['gender']); ?></span>
              <?php endif; ?>
            </div>
          </div>
        </fieldset>

        <!-- Address Information Section -->
        <fieldset class="form-section">
          <legend class="section-legend">
            <i class="fas fa-map-marker-alt"></i>
            Address Information
          </legend>
          
          <div class="form-group">
            <label for="street" class="form-label">
              <i class="fas fa-road"></i>
              Street Address *
            </label>
            <div class="input-wrapper">
              <input type="text" id="street" name="street" maxlength="40"
                     value="<?php echo isset($field_data['street']) ? htmlspecialchars($field_data['street']) : ''; ?>"
                     class="form-input <?php echo (isset($form_errors['street'])) ? 'error' : ''; ?>" 
                     placeholder="Enter your street address" required>
              <i class="fas fa-home input-icon"></i>
            </div>
            <?php if (isset($form_errors['street'])): ?>
              <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['street']); ?></span>
            <?php endif; ?>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="suburb" class="form-label">
                <i class="fas fa-building"></i>
                Suburb/Town *
              </label>
              <div class="input-wrapper">
                <input type="text" id="suburb" name="suburb" maxlength="40"
                       value="<?php echo isset($field_data['suburb']) ? htmlspecialchars($field_data['suburb']) : ''; ?>"
                       class="form-input <?php echo (isset($form_errors['suburb'])) ? 'error' : ''; ?>" 
                       placeholder="Enter your suburb/town" required>
                <i class="fas fa-city input-icon"></i>
              </div>
              <?php if (isset($form_errors['suburb'])): ?>
                <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['suburb']); ?></span>
              <?php endif; ?>
            </div>
            
            <div class="form-group">
              <label for="state" class="form-label">
                <i class="fas fa-flag"></i>
                State *
              </label>
              <div class="input-wrapper">
                <select id="state" name="state" class="form-select <?php echo (isset($form_errors['state'])) ? 'error' : ''; ?>" required>
                  <option value="">Select state</option>
                  <?php
                  $australian_states = [
                    'VIC' => 'Victoria',
                    'NSW' => 'New South Wales', 
                    'QLD' => 'Queensland',
                    'WA' => 'Western Australia',
                    'SA' => 'South Australia',
                    'TAS' => 'Tasmania',
                    'ACT' => 'Australian Capital Territory',
                    'NT' => 'Northern Territory'
                  ];
                  $current_state = $field_data['state'] ?? '';
                  foreach ($australian_states as $value => $label): 
                  ?>
                    <option value="<?php echo $value; ?>" 
                      <?php echo ($current_state == $value) ? 'selected' : ''; ?>>
                      <?php echo $label; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php if (isset($form_errors['state'])): ?>
                <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['state']); ?></span>
              <?php endif; ?>
            </div>
            
            <div class="form-group">
              <label for="postcode" class="form-label">
                <i class="fas fa-mail-bulk"></i>
                Postcode *
              </label>
              <div class="input-wrapper">
                <input type="text" id="postcode" name="postcode" maxlength="4" pattern="\d{4}"
                       value="<?php echo isset($field_data['postcode']) ? htmlspecialchars($field_data['postcode']) : ''; ?>"
                       class="form-input <?php echo (isset($form_errors['postcode'])) ? 'error' : ''; ?>" 
                       placeholder="0000" required>
                <i class="fas fa-map-pin input-icon"></i>
              </div>
              <?php if (isset($form_errors['postcode'])): ?>
                <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['postcode']); ?></span>
              <?php endif; ?>
            </div>
          </div>
        </fieldset>

        <!-- Contact Information Section -->
        <fieldset class="form-section">
          <legend class="section-legend">
            <i class="fas fa-address-book"></i>
            Contact Information
          </legend>
          
          <div class="form-row">
            <div class="form-group">
              <label for="email" class="form-label">
                <i class="fas fa-envelope"></i>
                Email Address *
              </label>
              <div class="input-wrapper">
                <input type="email" id="email" name="email"
                       value="<?php echo isset($field_data['email']) ? htmlspecialchars($field_data['email']) : ''; ?>"
                       class="form-input <?php echo (isset($form_errors['email'])) ? 'error' : ''; ?>" 
                       placeholder="your.email@example.com" required>
                <i class="fas fa-at input-icon"></i>
              </div>
              <?php if (isset($form_errors['email'])): ?>
                <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['email']); ?></span>
              <?php endif; ?>
            </div>
            
            <div class="form-group">
              <label for="phone" class="form-label">
                <i class="fas fa-phone"></i>
                Phone Number *
              </label>
              <div class="input-wrapper">
                <input type="tel" id="phone" name="phone" pattern="[0-9 ]{8,12}"
                       value="<?php echo isset($field_data['phone']) ? htmlspecialchars($field_data['phone']) : ''; ?>"
                       class="form-input <?php echo (isset($form_errors['phone'])) ? 'error' : ''; ?>" 
                       placeholder="04XX XXX XXX" required>
                <i class="fas fa-mobile-alt input-icon"></i>
              </div>
              <?php if (isset($form_errors['phone'])): ?>
                <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['phone']); ?></span>
              <?php endif; ?>
            </div>
          </div>
        </fieldset>

        <!-- Skills & Qualifications Section -->
        <fieldset class="form-section">
          <legend class="section-legend">
            <i class="fas fa-laptop-code"></i>
            Skills & Qualifications
          </legend>
          
            <div class="form-group">
                <fieldset class="skills-fieldset">
                    <legend class="form-label">
                        <i class="fas fa-cogs"></i>
                        Technical Skills *
                    </legend>
                    <div class="skills-grid">
                        <?php
                        $technical_skills = [
                            'HTML' => 'HTML5', 
                            'CSS' => 'CSS3',
                            'JavaScript' => 'JavaScript',
                            'Python' => 'Python',
                            'PHP' => 'PHP',
                            'MySQL' => 'MySQL',
                            'Java' => 'Java',
                            'React' => 'React',
                            'Node.js' => 'Node.js',
                            'Git' => 'Git',
                            'AWS' => 'AWS',
                            'Docker' => 'Docker'
                        ];
                        $current_skills = $field_data['skills'] ?? [];
                        foreach ($technical_skills as $value => $label): 
                        ?>
                            <label class="skill-option">
                                <input type="checkbox" name="skills[]" value="<?php echo $value; ?>"
                                      <?php echo (in_array($value, $current_skills)) ? 'checked' : ''; ?>>
                                <span class="checkmark">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="skill-label"><?php echo $label; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
                <?php if (isset($form_errors['skills'])): ?>
                    <span class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($form_errors['skills']); ?></span>
                <?php endif; ?>
            </div>

          <div class="form-group">
            <label for="otherSkills" class="form-label">
              <i class="fas fa-graduation-cap"></i>
              Other Skills & Experience
              <span class="optional-badge">Optional</span>
            </label>
            <div class="input-wrapper">
              <textarea id="otherSkills" name="otherSkills" rows="5" 
                        class="form-textarea" 
                        placeholder="Tell us about your other skills, certifications, projects, or any additional information that might be relevant to your application..."><?php echo isset($field_data['otherSkills']) ? htmlspecialchars($field_data['otherSkills']) : ''; ?></textarea>
              <i class="fas fa-edit textarea-icon"></i>
            </div>
          </div>
        </fieldset>

        <!-- Form Submission -->
        <div class="form-actions">
          <button type="submit" class="submit-button">
            <span class="button-text">Submit Application</span>
            <span class="button-icon">
              <i class="fas fa-paper-plane"></i>
            </span>
          </button>
          <p class="form-note"><i class="fas fa-info-circle"></i> Required fields must be filled out completely</p>
        </div>
      </form>
    </div>

<?php
// Include footer
include 'footer.inc';
?>
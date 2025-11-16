<?php
// Include the database settings and connect
require_once("settings.php");

$conn = @mysqli_connect($host, $user, $pass, $dbname)
    or die("<p>Database connection failure.</p>");

$feedback = ""; // To store success/error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    // --- Switch to handle all actions ---
    switch ($action) {
        case "list_all":
            $query = "SELECT * FROM eoi ORDER BY job_ref_number, last_name";
            $result = mysqli_query($conn, $query);
            $feedback = "### All Expressions of Interest";
            break;

        case "list_by_position":
            $job_ref = mysqli_real_escape_string($conn, $_POST["job_ref"]);
            $query = "SELECT * FROM eoi WHERE job_ref_number = '$job_ref' ORDER BY last_name";
            $result = mysqli_query($conn, $query);
            $feedback = "### EOIs for Position: $job_ref";
            break;

        case "list_by_applicant":
            $first_name = mysqli_real_escape_string($conn, $_POST["first_name"] ?? "");
            $last_name = mysqli_real_escape_string($conn, $_POST["last_name"] ?? "");

            $conditions = [];
            if (!empty($first_name)) {
                $conditions[] = "first_name LIKE '%$first_name%'";
            }
            if (!empty($last_name)) {
                $conditions[] = "last_name LIKE '%$last_name%'";
            }

            if (empty($conditions)) {
                $feedback = "<p style='color: red;'>Please enter a first name, last name, or both.</p>";
                $result = false; // Prevents display loop
                break;
            }

            $where_clause = implode(" AND ", $conditions);
            $query = "SELECT * FROM eoi WHERE $where_clause ORDER BY last_name";
            $result = mysqli_query($conn, $query);
            $feedback = "### EOIs for Applicant: " . trim("$first_name $last_name");
            break;

        case "delete_by_job":
            $job_ref_del = mysqli_real_escape_string($conn, $_POST["job_ref_del"]);
            $delete_query = "DELETE FROM eoi WHERE job_ref_number = '$job_ref_del'";
            if (mysqli_query($conn, $delete_query)) {
                $affected = mysqli_affected_rows($conn);
                $feedback = "<p style='color: green;'> Successfully deleted **$affected** EOIs for Job Reference: **$job_ref_del**.</p>";
            } else {
                $feedback = "<p style='color: red;'> Error deleting records: " . mysqli_error($conn) . "</p>";
            }
            $result = false;
            break;

        case "change_status":
            $eoi_id = mysqli_real_escape_string($conn, $_POST["eoi_id"]);
            $new_status = mysqli_real_escape_string($conn, $_POST["new_status"]);
            
            // Validate status
            $valid_statuses = ['New', 'Current', 'Final'];
            if (!in_array($new_status, $valid_statuses)) {
                $feedback = "<p style='color: red;'> Invalid status selected.</p>";
                $result = false;
                break;
            }

            $update_query = "UPDATE eoi SET status = '$new_status' WHERE EOI_number = '$eoi_id'";
            if (mysqli_query($conn, $update_query)) {
                if (mysqli_affected_rows($conn) > 0) {
                     $feedback = "<p style='color: green;'> EOI **$eoi_id** status successfully changed to **$new_status**.</p>";
                } else {
                     $feedback = "<p style='color: orange;'> EOI **$eoi_id** not found or status is already **$new_status**.</p>";
                }
            } else {
                $feedback = "<p style='color: red;'> Error updating EOI: " . mysqli_error($conn) . "</p>";
            }
            $result = false;
            break;

        default:
            $feedback = "";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EOI Management</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 900px; margin: auto; padding: 20px; }
        .form-section { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input[type="text"], select { padding: 8px; margin-top: 5px; width: 100%; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>EOI Management Portal</h1>
        <hr>

        <?php echo $feedback; // Display feedback/results heading ?>

        <?php if (isset($result) && $result !== false && mysqli_num_rows($result) > 0): ?>
            <p>Total Records Found: **<?php echo mysqli_num_rows($result); ?>**</p>
            <table>
                <thead>
                    <tr>
                        <th>EOI ID</th>
                        <th>Job Ref</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Status</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['EOI_number']; ?></td>
                        <td><?php echo $row['job_ref_number']; ?></td>
                        <td><?php echo $row['first_name']; ?></td>
                        <td><?php echo $row['last_name']; ?></td>
                        <td>**<?php echo $row['status']; ?>**</td>
                        <td><?php echo $row['email']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php mysqli_free_result($result); ?>
        <?php elseif (isset($result) && $result !== false): ?>
            <p>No records found for this query.</p>
        <?php endif; ?>

        <hr>

        <div class="form-section">
            <form method="post" action="manage.php">
                <input type="hidden" name="action" value="list_all">
                <h2>1. List All EOIs</h2>
                <p>Retrieves all Expressions of Interest records.</p>
                <button type="submit">List All EOIs</button>
            </form>
        </div>

        <div class="form-section">
            <form method="post" action="manage.php">
                <input type="hidden" name="action" value="list_by_position">
                <h2>2. List EOIs by Position</h2>
                <label for="job_ref">Job Reference Number:</label>
                <input type="text" id="job_ref" name="job_ref" required>
                <button type="submit">List EOIs for this Position</button>
            </form>
        </div>

        <div class="form-section">
            <form method="post" action="manage.php">
                <input type="hidden" name="action" value="list_by_applicant">
                <h2>3. List EOIs by Applicant Name</h2>
                <p>You can search by first name, last name, or both (partial matches allowed).</p>
                <label for="first_name">First Name (optional):</label>
                <input type="text" id="first_name" name="first_name">
                <label for="last_name">Last Name (optional):</label>
                <input type="text" id="last_name" name="last_name">
                <button type="submit">List EOIs by Applicant</button>
            </form>
        </div>

        <div class="form-section">
            <form method="post" action="manage.php" onsubmit="return confirm('Are you sure you want to DELETE all EOIs for this job? This action cannot be undone.');">
                <input type="hidden" name="action" value="delete_by_job">
                <h2>4. Delete EOIs by Job Reference</h2>
                <p style="color: red; font-weight: bold;"> Danger: This will permanently delete records.</p>
                <label for="job_ref_del">Job Reference Number to Delete:</label>
                <input type="text" id="job_ref_del" name="job_ref_del" required>
                <button type="submit" style="background-color: darkred;">Delete All EOIs for this Job</button>
            </form>
        </div>

        <div class="form-section">
            <form method="post" action="manage.php">
                <input type="hidden" name="action" value="change_status">
                <h2>5. Change EOI Status</h2>
                <label for="eoi_id">EOI Number (ID):</label>
                <input type="text" id="eoi_id" name="eoi_id" required>
                <label for="new_status">New Status:</label>
                <select id="new_status" name="new_status" required>
                    <option value="New">New</option>
                    <option value="Current">Current</option>
                    <option value="Final">Final</option>
                </select>
                <button type="submit">Update EOI Status</button>
            </form>
        </div>

        <?php mysqli_close($conn); ?>
    </div>
</body>
</html>
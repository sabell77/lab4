<?php
$databaseHost = 'localhost';
$databaseName = 'lab4_db';     
$databaseUsername = 'isabella';
$databasePassword = '850Linni#';

$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. First Name
    if (empty($_POST["fname"])) {
        $errors[] = "First Name is required.";
    } else {
        $fname = test_input($_POST["fname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $fname)) {
            $errors[] = "Only letters and spaces allowed in First Name.";
        }
    }

    // 2. Last Name
    if (empty($_POST["lname"])) {
        $errors[] = "Last Name is required.";
    } else {
        $lname = test_input($_POST["lname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $lname)) {
            $errors[] = "Only letters and spaces allowed in Last Name.";
        }
    }

    // 3. Email
    if (empty($_POST["email"])) {
        $errors[] = "Email is required.";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
    }

    // 4. Phone (Matches your form's 8-10 digits without +60 prefix)
    if (empty($_POST["phone"])) {
        $errors[] = "Phone number is required.";
    } else {
        $phone = test_input($_POST["phone"]);
        if (!preg_match("/^[0-9]{8,10}$/", $phone)) {
            $errors[] = "Phone number must be 8 to 10 digits without leading zero.";
        } else {
            $phone = "+60" . $phone;
        }
    }

    // 5. Date of Birth
    if (empty($_POST["dob"])) {
        $errors[] = "Date of Birth is required.";
    } else {
        $dob = test_input($_POST["dob"]);
    }

    // 6. Gender
    if (empty($_POST["gender"])) {
        $errors[] = "Gender is required.";
    } else {
        $gender = test_input($_POST["gender"]);
    }

    // 7. Street
    if (empty($_POST["street"])) {
        $errors[] = "Street address is required.";
    } else {
        $street = test_input($_POST["street"]);
    }

    // 8. State
    if (empty($_POST["state"])) {
        $errors[] = "State is required.";
    } else {
        $state = test_input($_POST["state"]);
    }

    // 9. City
    if (empty($_POST["city"])) {
        $errors[] = "City is required.";
    } else {
        $city = test_input($_POST["city"]);
    }

    // 10. Country
    if (empty($_POST["country"])) {
        $errors[] = "Country is required.";
    } else {
        $country = test_input($_POST["country"]);
    }

    // 11. Postcode (5 digits)
    if (empty($_POST["postcode"])) {
        $errors[] = "Postcode is required.";
    } else {
        $postcode = test_input($_POST["postcode"]);
        if (!preg_match("/^[0-9]{5}$/", $postcode)) {
            $errors[] = "Postcode must be 5 digits.";
        }
    }

    // 12. Username (4-20 characters)
    if (empty($_POST["username"])) {
        $errors[] = "Username is required.";
    } else {
        $username = test_input($_POST["username"]);
        if (!preg_match("/^[a-zA-Z0-9_]{4,20}$/", $username)) {
            $errors[] = "Username must be 4-20 characters (letters, numbers, or underscores).";
        }
    }

    // 13. Password (Matches your JS password rules)
    if (empty($_POST["password"])) {
        $errors[] = "Password is required.";
    } else {
        $raw_password = $_POST["password"];
        if (strlen($raw_password) < 8 ||
            !preg_match("/[a-z]/", $raw_password) ||
            !preg_match("/[A-Z]/", $raw_password) ||
            !preg_match("/\d/", $raw_password) ||
            !preg_match("/[!@#$%^&*()_+\-=\[\]{};':\"\\\\|,.<>\/?]/", $raw_password)) {
            $errors[] = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
        } else {
            $password = password_hash($raw_password, PASSWORD_DEFAULT);
        }
    }

    // 14. Terms and Conditions Checkbox
    if (empty($_POST["accept"])) {
        $errors[] = "You must accept the Terms and Conditions.";
    } else {
        $accept = "Accepted";
    }

    // Insert into database if no validation errors
    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO registration (fname, lname, email, phone, dob, gender, street, state, city, country, postcode, username, password, accept) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssss", $fname, $lname, $email, $phone, $dob, $gender, $street, $state, $city, $country, $postcode, $username, $password, $accept);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status</title>
    <link rel="stylesheet" type="text/css" href="104426.css">
    <link rel="stylesheet" type="text/css" href="theme.css">
    <style>
        .status-banner {
            padding: 15px 20px;
            border: 2px solid var(--primary);
            border-radius: 6px;
            box-shadow: 4px 4px 0px var(--primary);
            margin-bottom: 25px;
            font-weight: bold;
        }
        .status-success {
            background-color: #e8f5e9;
            color: #1b5e20;
        }
        .status-error {
            background-color: #ffebee;
            color: #b71c1c;
        }
        .summary-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .summary-list li {
            padding: 8px 0;
            border-bottom: 1px dashed var(--primary);
            font-size: 15px;
        }
        .summary-list li:last-child {
            border-bottom: none;
        }
        .error-list {
            margin: 10px 0 0 20px;
            font-weight: bold;
        }
        .error-list li {
            margin-bottom: 6px;
        }
        .btn-link {
            display: inline-block;
            text-decoration: none;
            padding: 12px 25px;
            background-color: var(--accent);
            color: var(--secondary);
            border: 2px solid var(--primary);
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn-link:hover {
            box-shadow: 4px 4px 0px var(--primary);
            transform: translate(-2px, -2px);
        }
    </style>
</head>
<body>

    <h1>Registration Status</h1>

    <div class="form-container">
        <?php if ($_SERVER["REQUEST_METHOD"] != "POST"): ?>
            <div class="status-banner status-error">
                No form data submitted. Please fill out the registration form first.
            </div>
            <div class="button-group">
                <a href="form.html" class="btn-link">Go to Registration Form</a>
            </div>

        <?php elseif ($success): ?>
            <div class="status-banner status-success">
                ✔ Registration Successful! Your data has been saved to the database.
            </div>

            <fieldset>
                <legend>Submitted Details</legend>
                <ul class="summary-list">
                    <li><strong>Full Name:</strong> <?php echo $fname . " " . $lname; ?></li>
                    <li><strong>Username:</strong> <?php echo $username; ?></li>
                    <li><strong>Email:</strong> <?php echo $email; ?></li>
                    <li><strong>Phone:</strong> <?php echo $phone; ?></li>
                    <li><strong>Date of Birth:</strong> <?php echo $dob; ?></li>
                    <li><strong>Gender:</strong> <?php echo ucfirst($gender); ?></li>
                    <li><strong>Address:</strong> <?php echo "$street, $postcode $city, $state, $country"; ?></li>
                </ul>
            </fieldset>

            <div class="button-group">
                <a href="form.html" class="btn-link">Register Another Account</a>
            </div>

        <?php else: ?>
            <div class="status-banner status-error">
                ✖ Validation Failed! Please fix the following errors:
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="button-group">
                <a href="javascript:history.back()" class="btn-link">Go Back and Fix</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <a href="index.html">Back to Main Page</a>
    </div>

</body>
</html>
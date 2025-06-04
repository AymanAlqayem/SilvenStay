<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        main {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 40px;
            width: 100%;
            max-width: 440px;
        }

        h1 {
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 32px;
            font-size: 24px;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        fieldset {
            border: none;
            padding: 0;
            margin: 0;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #4d4d4d;
            font-size: 14px;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
            background-color: #fafafa;
        }

        input:focus {
            outline: none;
            border-color: #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.15);
            background-color: white;
        }

        button[type="submit"] {
            background-color: #4a6cf7;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 8px;
        }

        button[type="submit"]:hover {
            background-color: #3a5ce4;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 38px;
            background: none;
            border: none;
            color: #808080;
            cursor: pointer;
            padding: 4px;
            font-size: 18px;
        }

        .password-strength {
            height: 4px;
            background-color: #f0f0f0;
            border-radius: 2px;
            margin-top: 12px;
            overflow: hidden;
        }

        .strength-meter {
            height: 100%;
            width: 0;
            transition: width 0.3s, background-color 0.3s;
        }

        .requirements {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #f0f0f0;
        }

        .requirements h2 {
            font-size: 15px;
            color: #4d4d4d;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .requirement-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 16px;
        }

        .requirement {
            display: flex;
            align-items: center;
            font-size: 13px;
            color: #666;
        }

        .requirement::before {
            content: "";
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-right: 8px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23d1d1d1'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
        }

        .requirement.valid {
            color: #1a1a1a;
        }

        .requirement.valid::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2328a745'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z'/%3E%3C/svg%3E");
        }

        .password-match {
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            color: #666;
        }

        .password-match.valid {
            color: #28a745;
        }

        .password-match.invalid {
            color: #dc3545;
        }

        .password-match::before {
            content: "";
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-right: 6px;
        }

        .password-match.valid::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2328a745'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z'/%3E%3C/svg%3E");
        }

        .password-match.invalid::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23dc3545'%3E%3Cpath d='M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z'/%3E%3C/svg%3E");
        }

        @media (max-width: 480px) {
            main {
                padding: 24px;
            }

            .requirement-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<main>
    <h1>Update Your Password</h1>
    <form id="passwordForm">
        <fieldset>
            <label for="oldPassword">Current Password</label>
            <input type="password" id="oldPassword" required placeholder="Enter your current password">
            <button type="button" class="password-toggle" aria-label="Toggle password visibility">👁️</button>
        </fieldset>

        <fieldset>
            <label for="newPassword">New Password</label>
            <input type="password" id="newPassword" required placeholder="Create a new password">
            <button type="button" class="password-toggle" aria-label="Toggle password visibility">👁️</button>
            <div class="password-strength">
                <div class="strength-meter" id="strengthMeter"></div>
            </div>
            <p id="passwordMatch" class="password-match"></p>
        </fieldset>

        <fieldset>
            <label for="confirmPassword">Confirm New Password</label>
            <input type="password" id="confirmPassword" required placeholder="Re-enter your new password">
            <button type="button" class="password-toggle" aria-label="Toggle password visibility">👁️</button>
        </fieldset>

        <aside class="requirements">
            <h2>Password Requirements</h2>
            <div class="requirement-list">
                <p class="requirement" id="length">8-20 characters</p>
                <p class="requirement" id="uppercase">1 uppercase letter</p>
                <p class="requirement" id="lowercase">1 lowercase letter</p>
                <p class="requirement" id="number">1 number (0-9)</p>
                <p class="requirement" id="special">1 special character</p>
                <p class="requirement" id="noSpaces">No spaces</p>
            </div>
        </aside>

        <button type="submit">Update Password</button>
    </form>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.password-toggle');
        const oldPassword = document.getElementById('oldPassword');
        const newPassword = document.getElementById('newPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const strengthMeter = document.getElementById('strengthMeter');
        const passwordMatch = document.getElementById('passwordMatch');
        const requirements = {
            length: document.getElementById('length'),
            uppercase: document.getElementById('uppercase'),
            lowercase: document.getElementById('lowercase'),
            number: document.getElementById('number'),
            special: document.getElementById('special'),
            noSpaces: document.getElementById('noSpaces')
        };

        // Toggle password visibility
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = '🔒';
                } else {
                    input.type = 'password';
                    this.textContent = '👁️';
                }
            });
        });

        // Check password strength and rules
        newPassword.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let validRules = 0;
            const totalRules = Object.keys(requirements).length;

            // Check length (8-20 characters)
            if (password.length >= 8 && password.length <= 20) {
                requirements.length.classList.add('valid');
                validRules++;
                strength += 15;
            } else {
                requirements.length.classList.remove('valid');
            }

            // Check uppercase
            if (/[A-Z]/.test(password)) {
                requirements.uppercase.classList.add('valid');
                validRules++;
                strength += 15;
            } else {
                requirements.uppercase.classList.remove('valid');
            }

            // Check lowercase
            if (/[a-z]/.test(password)) {
                requirements.lowercase.classList.add('valid');
                validRules++;
                strength += 15;
            } else {
                requirements.lowercase.classList.remove('valid');
            }

            // Check number
            if (/[0-9]/.test(password)) {
                requirements.number.classList.add('valid');
                validRules++;
                strength += 15;
            } else {
                requirements.number.classList.remove('valid');
            }

            // Check special char
            if (/[^A-Za-z0-9]/.test(password)) {
                requirements.special.classList.add('valid');
                validRules++;
                strength += 20;
            } else {
                requirements.special.classList.remove('valid');
            }

            // Check no spaces
            if (!/\s/.test(password)) {
                requirements.noSpaces.classList.add('valid');
                validRules++;
                strength += 20;
            } else {
                requirements.noSpaces.classList.remove('valid');
            }

            // Update strength meter
            strengthMeter.style.width = Math.min(strength, 100) + '%';
            if (validRules < 3) {
                strengthMeter.style.backgroundColor = '#dc3545';
            } else if (validRules < totalRules) {
                strengthMeter.style.backgroundColor = '#fd7e14';
            } else {
                strengthMeter.style.backgroundColor = '#28a745';
            }

            // Check password match
            checkPasswordMatch();
        });

        // Check password match on confirm field
        confirmPassword.addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            if (newPassword.value && confirmPassword.value) {
                if (newPassword.value === confirmPassword.value) {
                    passwordMatch.textContent = "Passwords match";
                    passwordMatch.className = "password-match valid";
                    confirmPassword.setCustomValidity('');
                } else {
                    passwordMatch.textContent = "Passwords don't match";
                    passwordMatch.className = "password-match invalid";
                    confirmPassword.setCustomValidity("Passwords don't match");
                }
            } else {
                passwordMatch.textContent = "";
                passwordMatch.className = "password-match";
            }
        }

        // Form submission
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Check if all requirements are met
            const allValid = Object.values(requirements).every(el => el.classList.contains('valid'));
            const passwordsMatch = newPassword.value === confirmPassword.value;

            if (allValid && passwordsMatch) {
                alert('Password changed successfully!');
                this.reset();
                strengthMeter.style.width = '0';
                passwordMatch.textContent = "";
                passwordMatch.className = "password-match";
                Object.values(requirements).forEach(el => el.classList.remove('valid'));
            } else if (!passwordsMatch) {
                alert('Please make sure your passwords match');
            } else {
                alert('Please meet all password requirements');
            }
        });
    });
</script>
</body>
</html>
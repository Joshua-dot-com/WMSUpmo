<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom CSS for the dropdown */
        .custom-select {
            appearance: none;
            transition: all 0.3s ease;
        }
        
        .select-wrapper:hover .select-arrow {
            transform: translateY(-50%) rotate(180deg);
        }
        
        .select-arrow {
            transition: transform 0.3s ease;
            top: 50%;
            transform: translateY(-50%);
        }
        
        /* Password strength bar */
        .password-strength {
            height: 5px;
            background-color: #e5e7eb;
            border-radius: 9999px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        
        /* Transition for showing/hiding conditional fields */
        .conditional-field {
            max-height: 100px;
            opacity: 1;
            transition: max-height 0.3s ease, opacity 0.3s ease, margin 0.3s ease;
            overflow: hidden;
        }
        
        .conditional-field.hidden {
            max-height: 0;
            opacity: 0;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="max-w-md w-full space-y-8">

        <!-- Signup Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Signup Form -->
            <form id="signup-form" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input 
                            type="text" 
                            id="first-name" 
                            name="first-name" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            required
                        >
                    </div>
                    <div>
                        <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input 
                            type="text" 
                            id="last-name" 
                            name="last-name" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            required
                        >
                    </div>
                </div>

                <!-- Role Selection -->
                <div>
                    <label for="roleSelect" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <div class="select-wrapper relative">
                        <select 
                            id="roleSelect" 
                            name="role" 
                            class="custom-select w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            required
                        >
                            <option value="User" selected>User</option>
                            <option value="Administrative Officials">Administrative Officials</option>
                        </select>
                        <!-- Custom arrow -->
                        <div class="select-arrow pointer-events-none absolute right-0 mr-3 text-gray-400">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <!-- College Dropdown - For User role -->
                <div id="collegeFieldContainer" class="conditional-field">
                    <label for="collegeSelect" class="block text-sm font-medium text-gray-700 mb-1">College</label>
                    <div class="select-wrapper relative">
                        <select 
                            id="collegeSelect" 
                            name="college" 
                            class="custom-select w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            required
                        >
                            <option value="" disabled selected>Select your college</option>
                            <option value="College of Teacher Education">College of Teacher Education</option>
                            <option value="College of Architecture">College of Architecture</option>
                            <option value="College of Social Work and Community Development">College of Social Work and Community Development</option>
                            <option value="College of Engineering">College of Engineering</option>
                            <option value="College of Nursing">College of Nursing</option>
                            <option value="College of Arts and Humanities">College of Arts and Humanities</option>
                            <option value="College of Science and Mathematics">College of Science and Mathematics</option>
                            <option value="College of Agriculture">College of Agriculture</option>
                            <option value="College of Forestry">College of Forestry</option>
                            <option value="College of Home Economics">College of Home Economics</option>
                            <option value="College of Nutrition and Dietetics">College of Nutrition and Dietetics</option>
                            <option value="College of Computer Science">College of Computer Science</option>
                            <option value="College of Criminology">College of Criminology</option>
                            <option value="College of Asian and Islamic Studies">College of Asian and Islamic Studies</option>
                        </select>
                        <!-- Custom arrow -->
                        <div class="select-arrow pointer-events-none absolute right-0 mr-3 text-gray-400">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <!-- Administrative Roles Dropdown - For Administrative Officials role -->
                <div id="adminRolesContainer" class="conditional-field hidden">
                    <label for="adminRolesSelect" class="block text-sm font-medium text-gray-700 mb-1">Administrative Role</label>
                    <div class="select-wrapper relative">
                        <select 
                            id="adminRolesSelect" 
                            name="adminRole" 
                            class="custom-select w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="" disabled selected>Select your administrative role</option>
                            <option value="Office of the President Staff">Office of the President Staff</option>
                            <option value="University and Board Secretary">University and Board Secretary</option>
                            <option value="Director">Director</option>
                            <option value="Campus Administrator">Campus Administrator</option>
                            <option value="Integrated Laboratory School Principal">Integrated Laboratory School Principal</option>
                            <option value="Integrated Laboratory School Asst Principal">Integrated Laboratory School Asst Principal</option>
                            <option value="Assistant Directors">Assistant Directors</option>
                            <option value="Associate Directors">Associate Directors</option>
                            <option value="Assistant Chairperson">Assistant Chairperson</option>
                            <option value="Special Assistant">Special Assistant</option>
                            <option value="Technical Assistant">Technical Assistant</option>
                            <option value="Technical Associate">Technical Associate</option>
                            <option value="Chairperson">Chairperson</option>
                            <option value="Manager">Manager</option>
                            <option value="Head / Chair of the Graduate School">Head / Chair of the Graduate School</option>
                            <option value="Coordinator">Coordinator</option>
                            <option value="Section Chief">Section Chief</option>
                            <option value="Moderator">Moderator</option>
                            <option value="Other Services">Other Services</option>
                        </select>
                        <!-- Custom arrow -->
                        <div class="select-arrow pointer-events-none absolute right-0 mr-3 text-gray-400">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <!-- Other Services Input - For "Other Services" option -->
                <div id="otherServicesContainer" class="conditional-field hidden">
                    <label for="otherServices" class="block text-sm font-medium text-gray-700 mb-1">Specify Other Services</label>
                    <input 
                        type="text" 
                        id="otherServices" 
                        name="otherServices" 
                        placeholder="Please specify your role"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="you@example.com" 
                            required
                        >
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="••••••••" 
                            required
                        >
                        <button 
                            type="button" 
                            id="toggle-password" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength mt-2">
                        <div class="password-strength-bar" id="password-strength-bar"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="password-strength-text">Password strength: Enter a password</p>
                </div>

                <div>
                    <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            id="confirm-password" 
                            name="confirm-password" 
                            class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="••••••••" 
                            required
                        >
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="password-match-text"></p>
                </div>

                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Password Requirements:</h3>
                    <ul class="text-xs text-gray-500 space-y-1">
                        <li id="req-length" class="flex items-center gap-2">
                            <i class="fas fa-circle text-xs"></i>
                            At least 8 characters long
                        </li>
                        <li id="req-uppercase" class="flex items-center gap-2">
                            <i class="fas fa-circle text-xs"></i>
                            At least one uppercase letter
                        </li>
                        <li id="req-lowercase" class="flex items-center gap-2">
                            <i class="fas fa-circle text-xs"></i>
                            At least one lowercase letter
                        </li>
                        <li id="req-number" class="flex items-center gap-2">
                            <i class="fas fa-circle text-xs"></i>
                            At least one number
                        </li>
                        <li id="req-special" class="flex items-center gap-2">
                            <i class="fas fa-circle text-xs"></i>
                            At least one special character
                        </li>
                    </ul>
                </div>

                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="terms" 
                        name="terms" 
                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-red-700 rounded"
                        required
                    >
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                    </label>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black-500"
                    >
                        Create Account
                    </button>
                </div>
            </form>
        </div>

        <!-- Login Link -->
        <div class="text-center mt-6">
            <p class="text-sm text-black-600">
                Already have an account? 
                <a href="Login.html" class="font-medium text-red-600 hover:underline">Sign in</a>
            </p>
        </div>
    </div>

    <script src="js/signup.js" defer></script>
</body>
</html>
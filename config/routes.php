<?php

$router = new Core\Router;

// Home routes
$router->add('/', ['controller' => 'Home', 'action' => 'index']);
$router->add('/home', ['controller' => 'Home', 'action' => 'index']);
$router->add('/list-trainers', ['controller' => 'Trainer', 'action' => 'list']);
$router->add('/list-packages', ['controller' => 'Packages', 'action' => 'listpackages']);
$router->add('/list-equipment', ['controller' => 'Equipment', 'action' => 'listEquipment']);
// $router->add('/equipment', ['controller' => 'Equipment', 'action' => 'listEquipment']);

// Admin routes
$router->add("/admin", ["controller" => "Admin", "action" => "index"]);
$router->add("/admin/dashboard", ["controller" => "Admin", "action" => "dashboard"]);
$router->add("/admin/admin-management", ["controller" => "Admin", "action" => "adminManagement"]);
$router->add("/admin/admin-management/create", ["controller" => "Admin", "action" => "create"]);
$router->add("/admin/admin-management/edit/{id:\d+}", ["controller" => "Admin", "action" => "edit"]);
$router->add("/admin/admin-management/delete/{id:\d+}", ["controller" => "Admin", "action" => "delete"]);

// Admin auth routes
$router->add("/admin-login", ["controller" => "Auth", "action" => "adminLogin"]);
$router->add("/admin/logout", ["controller" => "Auth", "action" => "logout"]);

// Admin Schedule routes
$router->add("/admin/schedule", ["controller" => "Schedule", "action" => "index"]);
$router->add("/admin/schedule/create", ["controller" => "Schedule", "action" => "create"]);
$router->add("/admin/schedule/update/{id:\d+}", ["controller" => "Schedule", "action" => "update"]);
$router->add("/admin/schedule/delete/{id:\d+}", ["controller" => "Schedule", "action" => "delete"]);

// Admin Trainer routes
$router->add('/admin/trainer', ['controller' => 'Trainer', 'action' => 'index']);
$router->add('/admin/trainer/create', ['controller' => 'Trainer', 'action' => 'create']);
$router->add('/admin/trainer/edit/{id:\d+}', ['controller' => 'Trainer', 'action' => 'edit']);
$router->add('/admin/trainer/delete/{id:\d+}', ['controller' => 'Trainer', 'action' => 'delete']);

$router->add('/admin/equipment', ['controller' => 'Equipment', 'action' => 'index']);
$router->add('/admin/equipment/create', ['controller' => 'Equipment', 'action' => 'create']);
$router->add('/admin/equipment/edit/{id:\d+}', ['controller' => 'Equipment', 'action' => 'edit']);
$router->add('/admin/equipment/delete/{id:\d+}', ['controller' => 'Equipment', 'action' => 'delete']);

$router->add('/admin/packages', ['controller' => 'packages', 'action' => 'index']);
$router->add('/admin/packages/create', ['controller' => 'packages', 'action' => 'create']);
$router->add('/admin/packages/edit/{id:\d+}', ['controller' => 'packages', 'action' => 'edit']);
$router->add('/admin/packages/delete/{id:\d+}', ['controller' => 'packages', 'action' => 'delete']);

$router->add('/admin/member', ['controller' => 'User', 'action' => 'adminIndex']);
$router->add('/admin/member/create', ['controller' => 'User', 'action' => 'createMember']);
$router->add('/admin/member/edit/{id:\d+}', ['controller' => 'User', 'action' => 'editMember']);
$router->add('/admin/member/delete/{id:\d+}', ['controller' => 'User', 'action' => 'deleteMember']);

$router->add('/admin/revenue', ['controller' => 'revenue', 'action' => 'index']);

// Admin Member routes
$router->add('/admin/member-management', ['controller' => 'Member', 'action' => 'index']);
$router->add('/admin/member-management/create', ['controller' => 'Member', 'action' => 'create']);
$router->add('/admin/member-management/update/{id:\d+}', ['controller' => 'Member', 'action' => 'edit']);
$router->add('/admin/member-management/delete/{id:\d+}', ['controller' => 'Member', 'action' => 'delete']);
$router->add('/admin/member-management/export', ['controller' => 'Member', 'action' => 'export']);

// Auth routes
$router->add("/login", ["controller" => "Auth", "action" => "login"]);
$router->add("/register", ["controller" => "Auth", "action" => "register"]);
$router->add("/logout", ["controller" => "Auth", "action" => "logout"]);

// Payment routes
$router->add('/payment/create', ['controller' => 'Payment', 'action' => 'create']);
$router->add('/payment/update-status/{id:\d+}', ['controller' => 'Payment', 'action' => 'updateStatus']);

// PT Registration routes
$router->add('/pt-registration', ['controller' => 'PTRegistration', 'action' => 'getAll']);
$router->add('/pt-registration/create', ['controller' => 'PTRegistration', 'action' => 'create']);
$router->add('/pt-registration/update/{id:\d+}', ['controller' => 'PTRegistration', 'action' => 'update']);
$router->add('/pt-registration/delete/{id:\d+}', ['controller' => 'PTRegistration', 'action' => 'delete']);

// Schedule routes
$router->add('/schedule', ['controller' => 'Schedule', 'action' => 'index']);
$router->add('/schedule/create', ['controller' => 'Schedule', 'action' => 'create']);

// Trainer routes
// $router->add('/trainers', ['controller' => 'Trainer', 'action' => 'dashboard']);
$router->add('/trainers/create', ['controller' => 'Trainer', 'action' => 'create']);
$router->add('/trainers/update/{id:\d+}', ['controller' => 'Trainer', 'action' => 'update']);
$router->add('/trainers/delete/{id:\d+}', ['controller' => 'Trainer', 'action' => 'delete']);
$router->add('/trainers/{id:\d+}/schedule', ['controller' => 'Trainer', 'action' => 'getSchedule']);
$router->add('/trainers/{id:\d+}/sessions', ['controller' => 'Trainer', 'action' => 'getTrainingSessions']);
$router->add('/trainers/sessions/{id:\d+}/status', ['controller' => 'Trainer', 'action' => 'updateSessionStatus']);
$router->add('/trainers/{id:\d+}/performance', ['controller' => 'Trainer', 'action' => 'getPerformanceStats']);
$router->add('/trainers/{id:\d+}/clients', ['controller' => 'Trainer', 'action' => 'getClients']);
$router->add('/trainer/trainers-login', ['controller' => 'Trainer', 'action' => 'login']);
// $router->add('/trainer/login', ['controller' => 'Auth', 'action' => 'login']);
// $router->add('/trainer/logout', ['controller' => 'Auth', 'action' => 'logout']); 

// Trainer auth routes 
$router->add('/trainer/login', ['controller' => 'Trainer', 'action' => 'login']);
$router->add('/trainer/dashboard', ['controller' => 'Trainer', 'action' => 'dashboard']);
$router->add('/trainer/logout', ['controller' => 'Trainer', 'action' => 'logout']);

// User routes
$router->add('/user', ['controller' => 'User', 'action' => 'index']);
$router->add('/user/profile', ['controller' => 'User', 'action' => 'profile']);
$router->add('/user/profile/update', ['controller' => 'User', 'action' => 'updateProfile']);
$router->add('/user/upload-avatar', ['controller' => 'User', 'action' => 'uploadAvatar']);
$router->add('/user/addresses', ['controller' => 'User', 'action' => 'addresses']);
$router->add('/user/address/{id:\d+}', ['controller' => 'User', 'action' => 'getAddress']);
$router->add('/user/address/create', ['controller' => 'User', 'action' => 'createAddress']);
$router->add('/user/address/delete/{id:\d+}', ['controller' => 'User', 'action' => 'deleteAddress']);
$router->add('/user/address/{id:\d+}/default', ['controller' => 'User', 'action' => 'setDefaultAddress']);

// RegisTrainer routes
$router->add('/trainers-list', ['controller' => 'RegisTrainer', 'action' => 'index']);
$router->add('/trainer/{id:\d+}', ['controller' => 'RegisTrainer', 'action' => 'trainerDetail']);

// Contact route
$router->add('/contact', ['controller' => 'Contact', 'action' => 'index']);

$router->add('/membership/register/{id}', ['controller' => 'User', 'action' => 'showRegistrationForm']);
$router->add("/user/change-password", ["controller" => "User", "action" => "changePassword"]);

// Membership routes
$router->add('/membership/register/{id:\d+}', ['controller' => 'Membership', 'action' => 'register']);
$router->add('/membership/process-registration', ['controller' => 'Membership', 'action' => 'processRegistration']);
$router->add('/membership/my-memberships', ['controller' => 'Membership', 'action' => 'myMemberships']);
$router->add('/membership/cancel/{id:\d+}', ['controller' => 'Membership', 'action' => 'cancel']);

// Admin membership routes
$router->add('/admin/memberships', ['controller' => 'Membership', 'action' => 'adminIndex']);
$router->add('/admin/memberships/view/{id:\d+}', ['controller' => 'Membership', 'action' => 'adminView']);
$router->add('/admin/memberships/approve/{id:\d+}', ['controller' => 'Membership', 'action' => 'approve']);
$router->add('/admin/memberships/reject/{id:\d+}', ['controller' => 'Membership', 'action' => 'reject']);

// Trainer authentication routes

return $router;

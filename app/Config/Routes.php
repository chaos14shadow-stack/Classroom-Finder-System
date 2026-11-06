<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Authentication & Public Onboarding
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('register/student', 'Auth::studentRegister');
$routes->get('register/instructor', 'Auth::instructorRegister');
$routes->get('logout', 'Auth::logout');
$routes->post('logout', 'Auth::logout');

// Route aliases for convenience
$routes->get('rooms', 'Student::rooms', ['filter' => 'role:student']);
$routes->get('rooms/(:segment)', 'Student::roomDetail/$1', ['filter' => 'role:student']);
$routes->get('bookings', 'Instructor::bookings', ['filter' => 'role:instructor']);

// Shared Authenticated Profile Routes
$routes->group('profile', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Profile::index');
    $routes->post('update', 'Profile::update');
    $routes->post('change-password', 'Profile::changePassword');
});

// Student Portal (Protected by role:student)
$routes->group('student', ['filter' => 'role:student'], static function ($routes) {
    $routes->get('/', 'Student::dashboard');
    $routes->get('dashboard', 'Student::dashboard');
    $routes->get('rooms', 'Student::rooms');
    $routes->get('rooms/(:segment)', 'Student::roomDetail/$1');
    $routes->get('announcements', 'Student::announcements');
    $routes->get('profile', 'Student::profile');
});

// Instructor Portal (Protected by role:instructor)
$routes->group('instructor', ['filter' => 'role:instructor'], static function ($routes) {
    $routes->get('/', 'Instructor::dashboard');
    $routes->get('dashboard', 'Instructor::dashboard');
    $routes->get('bookings', 'Instructor::bookings');
    $routes->get('book', 'Instructor::bookSearch');
    $routes->get('book/search', 'Instructor::bookSearch');
    $routes->get('book/form', 'Instructor::bookRoom');
    $routes->post('book', 'Instructor::storeBooking');
    $routes->post('bookings/cancel/(:num)', 'Instructor::cancelBooking/$1');
    $routes->get('announcements', 'Instructor::announcements');
    $routes->get('profile', 'Instructor::profile');
});

// Admin Portal (Protected by role:admin)
$routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get('/', 'Admin::overview');
    $routes->get('dashboard', 'Admin::overview');
    $routes->get('overview', 'Admin::overview');

    // Room Management CRUD
    $routes->get('rooms', 'Admin::rooms');
    $routes->post('rooms', 'Admin::createRoom');
    $routes->post('rooms/update/(:num)', 'Admin::updateRoom/$1');
    $routes->post('rooms/delete/(:num)', 'Admin::deleteRoom/$1');

    // User Management
    $routes->get('users', 'Admin::users');
    $routes->post('users/toggle/(:num)', 'Admin::toggleUserStatus/$1');
    $routes->post('users/change-role/(:num)', 'Admin::changeUserRole/$1');

    // Booking Moderation
    $routes->get('bookings', 'Admin::bookings');
    $routes->post('bookings/approve/(:num)', 'Admin::approveBooking/$1');
    $routes->post('bookings/reject/(:num)', 'Admin::rejectBooking/$1');

    // Announcement Management CRUD
    $routes->get('announcements', 'Admin::announcements');
    $routes->post('announcements', 'Admin::storeAnnouncement');
    $routes->post('announcements/update/(:num)', 'Admin::updateAnnouncement/$1');
    $routes->post('announcements/delete/(:num)', 'Admin::deleteAnnouncement/$1');

    // Admin Profile
    $routes->get('profile', 'Admin::profile');
});

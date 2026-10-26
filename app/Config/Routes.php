<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Authentication & Onboarding
$routes->get('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->get('register/student', 'Auth::studentRegister');
$routes->get('register/instructor', 'Auth::instructorRegister');

// Student Portal
$routes->group('student', static function ($routes) {
    $routes->get('dashboard', 'Student::dashboard');
    $routes->get('rooms', 'Student::rooms');
    $routes->get('announcements', 'Student::announcements');
});

// Instructor Portal
$routes->group('instructor', static function ($routes) {
    $routes->get('dashboard', 'Instructor::dashboard');
    $routes->get('bookings', 'Instructor::bookings');
    $routes->get('book', 'Instructor::bookSearch');
    $routes->get('book/form', 'Instructor::bookRoom');
    $routes->get('book/search', 'Instructor::bookSearch');
    $routes->get('announcements', 'Instructor::announcements');
});

// Admin Portal
$routes->group('admin', static function ($routes) {
    $routes->get('/', 'Admin::overview');
    $routes->get('dashboard', 'Admin::overview');
    $routes->get('overview', 'Admin::overview');
    $routes->get('rooms', 'Admin::rooms');
    $routes->get('users', 'Admin::users');
    $routes->get('bookings', 'Admin::bookings');
    $routes->get('announcements', 'Admin::announcements');
});

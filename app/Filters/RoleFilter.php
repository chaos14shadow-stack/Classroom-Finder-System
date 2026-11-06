<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please log in to continue.');
        }

        $userRole = strtolower((string) $session->get('role'));
        $allowedRoles = is_array($arguments) ? array_map('strtolower', $arguments) : [strtolower((string) $arguments)];

        if (!in_array($userRole, $allowedRoles, true)) {
            // Return HTTP 403 Forbidden
            return response()
                ->setStatusCode(403)
                ->setBody(view('errors/html/error_403', [
                    'message' => 'You do not have administrative or authorization permissions to access this area.'
                ]));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}


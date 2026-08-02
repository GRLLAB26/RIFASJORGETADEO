<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    public function login()
    {
        view('Auth/Login');
    }


    public function authenticate()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);

        if (!$user) {
            exit('Usuario no encontrado');
        }

        if (!password_verify($password, $user->password)) {
            exit('Contraseña incorrecta');
        }

        session_start();

        $_SESSION['user'] = [
            'id'   => $user->id,
            'name' => $user->name,
            'role' => $user->role
        ];

        if ($user->role === 'admin') {
            header('Location: /admin');
        } else {
            header('Location: /client');
        }

        exit;
    }


    public function logout()
    {
        session_start();
        session_destroy();

        header('Location: /login');
        exit;
    }
}

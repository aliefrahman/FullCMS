<?php
namespace App\Models;

use App\Core\Database;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Find user by email
     * @param string $email
     * @return object|false
     */
    public function findByEmail($email)
    {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Find user by username
     * @param string $username
     * @return object|false
     */
    public function findByUsername($username)
    {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    /**
     * Register a new user
     * @param array $data
     * @return bool
     */
    public function register($data)
    {
        $this->db->query("INSERT INTO users (username, email, password, full_name, role, status) VALUES (:username, :email, :password, :full_name, :role, :status)");
        
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        // Use standard secure BCRYPT password hashing
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_BCRYPT));
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':role', $data['role'] ?? 'subscriber');
        $this->db->bind(':status', $data['status'] ?? 'active');
        
        return $this->db->execute();
    }
}

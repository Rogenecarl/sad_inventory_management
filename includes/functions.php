<?php

//-------------this is my login functions and security-----------------//
function authenticate_user($username, $password, $conn)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND status = 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && sha1($password) === $user['password']) {
        return $user;
    }
    return false;
}


function login_user($user)
{

    global $conn;

    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE User_id = :User_id");
    $stmt->execute(['User_id' => $user['User_id']]);

    session_start();
    $_SESSION['user_id'] = $user['User_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_level'] = $user['user_level'];

    if ($user['user_level'] == 1) {
        header("Location: pages/dev_dashboard.php");
    } elseif ($user['user_level'] == 2) {
        header("Location: pages/admin_dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}

//-------------this is the end of the login functions and security-----------------//



//-------------this is the start of the usermanagement functions-----------------//

// function createUser($conn, $data)
// {
//     try {
//         $hashed_password = sha1($data['password']);

//         $stmt = $conn->prepare("
//             INSERT INTO users
//             (
//                 name,
//                 username,
//                 password,
//                 user_level,
//                 status
//             )
//             VALUES
//             (
//                 :name,
//                 :username,
//                 :password,
//                 :role,
//                 :status
//             )
//         ");

//         $stmt->execute([
//             'name' => $data['name'],
//             'username' => $data['username'],
//             'password' => $hashed_password,
//             'role' => $data['role'],
//             'status' => $data['status'],
//         ]);

//         return $stmt->rowCount() > 0; 
//     } catch (PDOException $e) {
//         error_log("Error creating user: " . $e->getMessage());
//         return false;
//     }
// }

// function updateUser($conn, $userId, $data)
// {
//     try {
//         if (!empty($data['password'])) {
//             $hashed_password = sha1($data['password']);
//             $stmt = $conn->prepare("
//                 UPDATE users
//                 SET
//                     name = :name,
//                     username = :username,
//                     password = :password,
//                     user_level = :role,
//                     status = :status
//                 WHERE User_id = :userId
//             ");
//             $params = [
//                 'name' => $data['name'],
//                 'username' => $data['username'],
//                 'password' => $hashed_password,
//                 'role' => $data['role'],
//                 'status' => $data['status'],
//                 'userId' => $userId,
//             ];
//         } else {
//             $stmt = $conn->prepare("
//                 UPDATE users
//                 SET
//                     name = :name,
//                     username = :username,
//                     user_level = :role,
//                     status = :status
//                 WHERE User_id = :userId
//             ");
//             $params = [
//                 'name' => $data['name'],
//                 'username' => $data['username'],
//                 'role' => $data['role'],
//                 'status' => $data['status'],
//                 'userId' => $userId,
//             ];
//         }

//         $stmt->execute($params);

//         return $stmt->rowCount() > 0;
//     } catch (PDOException $e) {
//         error_log("Error updating user: " . $e->getMessage());
//         return false;
//     }
// }

// function deleteUser($conn, $userId)
// {
//     try {
//         $stmt = $conn->prepare("
//             DELETE FROM users
//             WHERE User_id = :userId
//         ");

//         $stmt->execute(['userId' => $userId]);

//         return $stmt->rowCount() > 0;
//     } catch (PDOException $e) {
//         error_log("Error deleting user: " . $e->getMessage());
//         return false;
//     }
// }
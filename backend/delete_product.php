<?php
// delete_product.php (FORCE DELETE - NOT RECOMMENDED)
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

include_once 'db_connection.php';

if (!$conn || $conn->connect_error) {
    $response['message'] = 'Database connection failed.';
    echo json_encode($response);
    exit;
}

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $response['message'] = 'Valid Product ID is required.';
} else {
    $product_id = intval($_GET['id']);
    $conn->begin_transaction();

    try {

        $conn->query("SET FOREIGN_KEY_CHECKS=0");

        $sql = "DELETE FROM products WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('i', $product_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response = ['status' => 'success', 'message' => 'Product force deleted successfully (WARNING: orphaned data may exist).'];
                    $conn->commit();
                } else {
                    $response['message'] = 'Product not found.';
                    $conn->rollback();
                }
            } else {
                $response['message'] = 'Database error during forced delete: ' . $stmt->error;
                $conn->rollback(); 
            }
            $stmt->close();
        } else {
            $response['message'] = 'Failed to prepare SQL delete query. Error: ' . $conn->error;
            $conn->rollback();
        }

        $conn->query("SET FOREIGN_KEY_CHECKS=1");

    } catch (Exception $e) {
        $response['message'] = 'An exception occurred during force delete: ' . $e->getMessage();
        $conn->rollback();
        try { $conn->query("SET FOREIGN_KEY_CHECKS=1"); } catch (Exception $ex) {}
    }

    // --- End Force Deletion ---

    $conn->close();
}

echo json_encode($response);

?>
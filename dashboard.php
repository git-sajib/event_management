<?php
// Start the session
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require 'includes/db.php';

// Pagination
$limit = 5; // Number of events per page
$page = $_GET['page'] ?? 1; // Current page
$offset = ($page - 1) * $limit; // Offset for SQL query

// Sorting
$sort = $_GET['sort'] ?? 'event_date'; // Default sort by event date
$order = $_GET['order'] ?? 'ASC'; // Default order (ASC or DESC)

// Filtering
$search_query = $_GET['search_query'] ?? '';

// Build the SQL query
$sql = "SELECT e.id AS event_id, e.name AS event_name, e.description, e.event_date, e.max_capacity, 
               a.name AS attendee_name, a.email, a.registered_at
        FROM events e
        LEFT JOIN attendees a ON e.id = a.event_id
        WHERE e.user_id = :user_id";

$params = ['user_id' => $_SESSION['user_id']];

if (!empty($search_query)) {
    $sql .= " AND (e.name LIKE :search_query OR a.email LIKE :search_query)";
    $params['search_query'] = "%$search_query%";
}

// Add sorting
$sql .= " ORDER BY $sort $order";

// Add pagination
$sql .= " LIMIT $limit OFFSET $offset";

// Fetch events
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

// Fetch total number of events for pagination
$total_events_sql = "SELECT COUNT(DISTINCT e.id) as total
                     FROM events e
                     LEFT JOIN attendees a ON e.id = a.event_id
                     WHERE e.user_id = :user_id";

if (!empty($search_query)) {
    $total_events_sql .= " AND (e.name LIKE :search_query OR a.email LIKE :search_query)";
}

$stmt = $pdo->prepare($total_events_sql);
$stmt->execute($params);
$total_events = $stmt->fetch()['total'];
$total_pages = ceil($total_events / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Event Dashboard</h1>
        <a href="create_event.php" class="btn btn-success mb-3">Create New Event</a>

        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <label for="search_query" class="form-label">Search Events and Attendees</label>
                    <input type="text" class="form-control" id="search_query" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Enter event name or attendee email">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="dashboard.php" class="btn btn-secondary ms-2">Reset</a>
                </div>
            </div>
        </form>

        <!-- Event Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        <a href="?sort=event_name&order=<?php echo $sort === 'event_name' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>&search_query=<?php echo $search_query; ?>">
                            Event Name <?php echo $sort === 'event_name' ? ($order === 'ASC' ? '▲' : '▼') : ''; ?>
                        </a>
                    </th>
                    <th>
                        <a href="?sort=event_date&order=<?php echo $sort === 'event_date' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>&search_query=<?php echo $search_query; ?>">
                            Event Date <?php echo $sort === 'event_date' ? ($order === 'ASC' ? '▲' : '▼') : ''; ?>
                        </a>
                    </th>
                    <th>Attendee Name</th>
                    <th>Attendee Email</th>
                    <th>Registered At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $current_event_id = null;
                $grouped_events = [];

                // Group events by event_id
                foreach ($events as $row) {
                    $event_id = $row['event_id'];
                    if (!isset($grouped_events[$event_id])) {
                        $grouped_events[$event_id] = [
                            'event_name' => $row['event_name'],
                            'event_date' => $row['event_date'],
                            'attendees' => []
                        ];
                    }
                    if (!empty($row['attendee_name'])) {
                        $grouped_events[$event_id]['attendees'][] = [
                            'name' => $row['attendee_name'],
                            'email' => $row['email'],
                            'registered_at' => $row['registered_at']
                        ];
                    }
                }

                // Display grouped events
                foreach ($grouped_events as $event_id => $event):
                    $attendees = $event['attendees'];
                    $rowspan = count($attendees) > 0 ? count($attendees) : 1;
                ?>
                <tr>
                    <td rowspan="<?php echo $rowspan; ?>"><?php echo htmlspecialchars($event['event_name']); ?></td>
                    <td rowspan="<?php echo $rowspan; ?>"><?php echo htmlspecialchars($event['event_date']); ?></td>
                    <?php if (!empty($attendees)): ?>
                        <td><?php echo htmlspecialchars($attendees[0]['name']); ?></td>
                        <td><?php echo htmlspecialchars($attendees[0]['email']); ?></td>
                        <td><?php echo htmlspecialchars($attendees[0]['registered_at']); ?></td>
                    <?php else: ?>
                        <td colspan="3">No attendees registered yet.</td>
                    <?php endif; ?>
                    <td rowspan="<?php echo $rowspan; ?>">
                        <a href="view_event.php?id=<?php echo $event_id; ?>" class="btn btn-info">View</a>
                        <a href="edit_event.php?id=<?php echo $event_id; ?>" class="btn btn-warning">Edit</a>
                        <a href="delete_event.php?id=<?php echo $event_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                    </td>
                </tr>
                <?php
                    if (count($attendees) > 1):
                        for ($i = 1; $i < count($attendees); $i++):
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($attendees[$i]['name']); ?></td>
                    <td><?php echo htmlspecialchars($attendees[$i]['email']); ?></td>
                    <td><?php echo htmlspecialchars($attendees[$i]['registered_at']); ?></td>
                </tr>
                <?php
                        endfor;
                    endif;
                endforeach;
                ?>
            </tbody>
        </table>

                <!-- Toast Messages -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <!-- Register Success Toast -->
    <div id="registerSuccessToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Attendee registered successfully!
        </div>
    </div>

    <!-- Other Toasts (if any) -->
    <!-- Success Toast -->
    <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Event created successfully!
        </div>
    </div>

    <!-- Edit Toast -->
    <div id="editToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Updated</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Event updated successfully!
        </div>
    </div>

    <!-- Delete Toast -->
    <div id="deleteToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto text-danger">Deleted</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Event deleted successfully!
        </div>
    </div>
    
</div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&search_query=<?php echo $search_query; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

 <!-- Footer -->
 <?php include 'includes/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/scripts.js"></script>
</body>
</html>



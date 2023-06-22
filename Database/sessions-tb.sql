-- your-database.sessions-table definition
-- Change table-name for the name of your sessions table

CREATE TABLE `table-name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(100) NOT NULL,
  `msisdn` varchar(100) NOT NULL,
  `network` varchar(100) DEFAULT 'unknown'
  `ussd_code` varchar(100) DEFAULT '*123*4#',
  `u_data` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='USSD application sessions data';
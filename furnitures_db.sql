-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 05:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `furnitures_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `zipcode` varchar(45) NOT NULL,
  `country` varchar(100) NOT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `recipient`, `street`, `barangay`, `city`, `province`, `zipcode`, `country`, `phone`, `user_id`) VALUES
(8, 'Jericho Bellen', '23 Dita Street', 'Western Bicutan', 'Taguig City', 'Metro Manila', '1630', 'Philippines', '09202558774', 1),
(10, 'jennie kim', '77 Seoul st.', 'Busan', 'Makati', 'Calabarzon', '1630', 'South Korea', '09202558778', 4),
(18, 'Ariana Grande', '456 New York street', 'Upper Bicutan', 'South Korea', 'Metro Manila', '5689', 'Philippines', '09202558774', 11),
(19, 'Sabrina Carpenter', '123 New York street', 'Metro Cebu', 'South Korea', 'Metro Manila', '1630', 'Philippines', '09202558774', 12),
(22, 'Olivia Rodrigo', '589 Dita Street', 'Lower Bicutan', 'South Korea', 'MIMAROPA', '5689', 'Philippines', '09202558778', 17),
(23, 'Katy Perry', '88 Seoul street', 'Manhattan', 'New York', 'New York State', '5689', 'Philippines', '09202558774', 18),
(24, 'Jericho Bellen', '99 New York street', 'Silangan', 'Cubao', 'Quezon City', '1234', 'USA', '09202558774', 1),
(26, 'Lisa', '1 Seoul st.', 'Silangan', 'Cubao', 'Quezon City', '1234', 'Philippines', '09202558774', 19),
(33, 'taylor swift', '33 Uptown St.', 'Fort Bonifacio', 'Makati', 'Metro Manila', '2587', 'Philippines', '12345678910', 23),
(34, 'joe alwyn', '31 New York', 'Hawaii', 'Harvard', 'MIMAROPA', '1478', 'Philippines', '9876543210', 23),
(37, 'kai', 'Dita Street', 'North Signal', 'South Korea', 'MIMAROPA', '1360', 'Philippines', '09202558774', 26);

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `name`) VALUES
(2, 'Ashley Furniture'),
(4, 'Home Suite'),
(1, 'IKEA'),
(5, 'Lexington'),
(3, 'Urban Concepts');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `user_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `cart_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`) VALUES
(2, 'Beds'),
(3, 'Cabinets'),
(4, 'Carpets'),
(6, 'Chairs'),
(1, 'Sofas'),
(5, 'Tables');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`, `reply`, `replied_at`) VALUES
(3, 'jennie kim', 'jenniekim@gmail.com', 'BEDS', 'is it free shipping?', '2025-11-14 17:37:41', 'yes', '2025-11-15 01:38:35'),
(4, 'jennie kim', 'jenniekim@gmail.com', 'DISCOUNTS', 'may discount po ba?', '2025-11-15 03:27:39', 'wala po.', '2025-11-15 11:30:23'),
(5, 'jennie kim', 'jenniekim@gmail.com', 'DELIVERY', 'gaano po katagal bago ma deliver?', '2025-11-15 03:29:00', '4-5 days', '2025-11-15 11:30:13'),
(6, 'taylor swift', 'taylorswift@gmail.com', 'OVERSEAS SHIPPING', 'pwede sa USA?', '2025-11-16 13:55:07', 'philippines only po', '2025-11-16 22:58:05');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `payment_method` varchar(45) DEFAULT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled','Received') DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `tracking_number`, `created_at`, `user_id`, `address_id`, `payment_method`, `status`, `cancelled_at`) VALUES
(24, 'ORD-20251112-9664', '2025-11-12 17:15:55', 1, 8, 'cod', 'Shipped', NULL),
(25, 'ORD-20251113-3052', '2025-11-13 02:54:11', 1, 8, 'cod', 'Shipped', NULL),
(26, 'ORD-20251113-8685', '2025-11-13 03:00:47', 1, 8, 'cod', 'Delivered', NULL),
(27, 'ORD-20251113-6882', '2025-11-13 03:09:28', 1, 24, 'cod', 'Delivered', NULL),
(28, 'ORD-20251113-0927', '2025-11-13 03:13:43', 1, 8, 'cod', 'Received', NULL),
(31, 'ORD-20251114-5999', '2025-11-14 11:13:33', 1, 8, 'cod', 'Shipped', NULL),
(32, 'ORD-20251114-3941', '2025-11-14 11:15:05', 1, 24, 'cod', 'Shipped', NULL),
(33, 'ORD-20251114-0994', '2025-11-14 11:44:19', 4, 10, 'cod', 'Received', NULL),
(35, 'ORD-20251114-1417', '2025-11-14 12:18:51', 1, 8, 'cod', 'Shipped', NULL),
(37, 'ORD-20251114-4811', '2025-11-14 12:35:59', 1, 8, 'cod', 'Shipped', NULL),
(40, 'ORD-20251115-1535', '2025-11-15 06:29:12', 19, 26, 'cod', 'Received', NULL),
(41, 'ORD-20251116-9512', '2025-11-15 12:15:44', 19, 26, 'cod', 'Shipped', NULL),
(44, 'ORD-20251116-1173', '2025-11-16 09:28:07', 19, 26, 'cod', 'Received', NULL),
(45, NULL, '2025-11-16 09:34:31', 19, 26, 'cod', 'Cancelled', '2025-11-16 17:35:03'),
(46, NULL, '2025-11-16 09:35:35', 19, 26, 'cod', 'Cancelled', '2025-11-16 17:36:58'),
(47, NULL, '2025-11-16 09:37:18', 19, 26, 'cod', 'Cancelled', NULL),
(48, 'ORD-20251116-5440', '2025-11-16 13:42:16', 23, 33, 'cod', 'Received', NULL),
(49, NULL, '2025-11-16 13:54:09', 23, 34, 'cod', 'Cancelled', '2025-11-16 21:54:29'),
(50, NULL, '2025-11-16 14:06:00', 23, 33, 'cod', 'Cancelled', '2025-11-16 22:11:24'),
(51, 'ORD-20251116-7588', '2025-11-16 14:26:06', 23, 34, 'cod', 'Delivered', NULL),
(52, 'ORD-20251116-1212', '2025-11-16 14:30:39', 1, 8, 'cod', 'Delivered', NULL),
(53, 'ORD-20251117-5744', '2025-11-17 03:56:03', 1, 8, 'cod', 'Shipped', NULL),
(55, 'ORD-20251117-3336', '2025-11-17 09:29:27', 11, 18, 'cod', 'Delivered', NULL),
(56, 'ORD-20251117-1351', '2025-11-17 09:36:00', 1, 8, 'cod', 'Delivered', NULL),
(57, 'ORD-20251117-1086', '2025-11-17 16:05:01', 1, 8, 'cod', 'Shipped', NULL),
(58, 'ORD-20251117-7568', '2025-11-17 22:46:27', 11, 18, 'cod', 'Delivered', NULL),
(59, 'ORD-20251118-8438', '2025-11-17 22:50:40', 11, 18, 'cod', 'Shipped', NULL),
(60, NULL, '2025-11-18 02:42:47', 26, 37, 'cod', 'Pending', NULL),
(66, 'ORD-20251118-0550', '2025-11-18 16:11:53', 11, 18, 'cod', 'Shipped', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_items_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_items_id`, `order_id`, `variant_id`, `quantity`, `price`) VALUES
(31, 24, 4, 2, 8998.00),
(32, 24, 8, 1, 8999.00),
(33, 25, 4, 3, 8998.00),
(34, 25, 8, 2, 8999.00),
(35, 26, 4, 2, 8998.00),
(36, 26, 8, 1, 8999.00),
(37, 27, 4, 1, 8998.00),
(38, 27, 8, 1, 8999.00),
(39, 28, 4, 3, 8998.00),
(40, 28, 8, 3, 8999.00),
(41, 28, 10, 3, 6999.00),
(44, 31, 25, 1, 9999.00),
(45, 32, 13, 2, 6999.00),
(46, 33, 8, 3, 8999.00),
(47, 33, 10, 1, 6999.00),
(48, 33, 25, 1, 9999.00),
(49, 33, 63, 1, 899.00),
(50, 33, 19, 1, 9999.00),
(52, 35, 4, 1, 8998.00),
(54, 37, 11, 1, 6999.00),
(57, 40, 4, 1, 8998.00),
(58, 41, 13, 1, 6999.00),
(62, 44, 17, 1, 8999.00),
(63, 44, 19, 1, 9999.00),
(64, 44, 21, 1, 8999.00),
(65, 45, 15, 1, 799.00),
(66, 46, 10, 1, 6999.00),
(67, 47, 41, 1, 9999.00),
(68, 48, 4, 2, 8998.00),
(69, 49, 9, 3, 8999.00),
(70, 50, 9, 2, 8999.00),
(71, 51, 5, 1, 12999.00),
(72, 52, 70, 2, 6999.00),
(73, 53, 5, 1, 12999.00),
(75, 55, 4, 2, 8998.00),
(76, 56, 62, 1, 1099.00),
(77, 57, 15, 2, 799.00),
(78, 58, 11, 2, 6999.00),
(79, 59, 13, 3, 6999.00),
(80, 60, 22, 1, 8999.00),
(81, 60, 8, 1, 8999.00),
(82, 60, 11, 1, 6999.00),
(88, 66, 10, 1, 6999.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `dimension` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `brand_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `dimension`, `created_at`, `brand_id`, `category_id`) VALUES
(1, 'Queen Platform Bed', 'Streamlined frame with built-in drawers for discreet underbed storage', '160 x 210 x 35 cm', '2025-11-06 10:51:17', 2, 2),
(2, 'Sectional Sofa with Chaise', 'Spacious L-shaped seating with a built-in chaise for lounging and entertaining.', '280 x 160 x 85 cm', '2025-11-06 12:15:13', 1, 1),
(3, 'Ergonomic Office Chair', 'Adjustable lumbar support and breathable mesh for all-day productivity.', '65 x 65 x 110 cm', '2025-11-06 12:16:05', 1, 6),
(4, 'Extendable Dining Table', 'Expandable surface accommodates guests while saving space when not in use.', '180 x 90 x 75 cm', '2025-11-06 12:16:52', 1, 5),
(5, 'Tall Pantry Cabinet', 'Vertical storage for kitchen staples, maximizing space in compact areas.', '80 x 40 x 200 cm', '2025-11-06 12:17:35', 1, 3),
(6, 'Jute Round Rug', 'Natural fiber and circular shape bring earthy texture to modern interiors.', '150 cm x 150cm x 1 cm', '2025-11-06 12:18:22', 1, 4),
(7, 'Reclining Sofa', 'Plush seating with adjustable recline for ultimate comfort and style.', '220 x 95 x 100 cm', '2025-11-07 16:13:15', 4, 1),
(8, 'Sleeper Sofa with Storage', 'Converts into a bed and includes hidden compartments for bedding or essentials.', '200 x 90 x 85 cm', '2025-11-07 16:14:03', 2, 1),
(9, 'Tufted Loveseat', 'Luxurious upholstery with button tufting, perfect for cozy corners.', '140 x 85 x 90 cm', '2025-11-07 16:15:17', 5, 1),
(11, 'Mid-Century Modern 3-Seater', 'Sleek lines and tapered legs bring retro charm to contemporary living rooms.', '210 x 90 x 85 cm', '2025-11-07 16:16:22', 5, 1),
(12, 'King Bed Frame', 'Padded headboard and side rails offer comfort and elegance in a spacious design.', '190 x 210 x 40 cm', '2025-11-07 16:19:12', 3, 2),
(13, 'Twin Bed with Headboard', 'Durable and minimalist, ideal for guest rooms or kids’ spaces.', '100 x 200 x 35 cm', '2025-11-07 16:20:04', 3, 2),
(14, 'Bunk Bed with Desk Combo', 'Space-saving solution combining sleep and study in one compact unit.', '200 x 100 x 180 cm', '2025-11-07 16:20:48', 4, 2),
(15, 'Canopy Bed with Slatted Base', 'Dramatic vertical posts and open slats create a breezy, romantic vibe.', '160 x 210 x 200 cm', '2025-11-07 16:21:45', 2, 2),
(16, 'Accent Armchair in Linen', 'Soft linen fabric sculpted arms add charm to any reading nook.', '75 x 85 x 90 cm', '2025-11-07 16:22:49', 5, 6),
(17, 'Rocking Chair with Cushions', 'Gentle motion and padded seat make it perfect for relaxation or nurseries.', '70 x 90 x 100 cm', '2025-11-07 16:23:24', 3, 6),
(18, 'Dining Chair Set', 'Classic craftsmanship for timeless dining room appeal.', '45 x 50 x 90 cm', '2025-11-07 16:24:40', 4, 6),
(19, 'Foldable Lounge Chair', 'Lightweight and collapsible, great for patios or flexible indoor seating.', '60 x 70 x 85 cm', '2025-11-07 16:25:18', 1, 6),
(20, 'Round Coffee Table with Storage', 'Circular design with hidden compartments for magazines and remotes.', '90 cm diameter x 40 cm', '2025-11-07 16:28:07', 1, 5),
(21, 'Console Table with Drawers', 'Slim profile with drawers for entryway organization or hallway styling.', '120 x 35 x 80 cm', '2025-11-07 16:31:27', 1, 5),
(22, 'Nesting Side Tables', 'Stackable tables that separate for versatile use in tight spaces.', '50 x 50 x 55 cm', '2025-11-07 16:33:01', 5, 5),
(23, 'Solid Wood Work Desk', 'Sturdy and spacious, ideal for focused work or creative projects.', '140 x 70 x 75 cm', '2025-11-07 16:33:50', 2, 5),
(24, 'TV Console with Shelves', 'Media-friendly design with open and closed shelving for electronics and décor.', '160 x 40 x 50 cm', '2025-11-07 16:35:02', 1, 3),
(25, 'Glass Display Cabinet', 'Transparent doors showcase collectibles while protecting them from dust.', '90 x 40 x 180 cm', '2025-11-07 16:35:40', 3, 3),
(26, 'Shoe Cabinet with Flip Doors', 'Tilt-out compartments keep footwear organized and out of sight.', '100 x 35 x 120 cm', '2025-11-07 16:36:31', 5, 3),
(27, 'Modular Storage Cube Cabinet', 'Stackable cubes for customizable organization in any room.', '35 x 35 x 35 cm', '2025-11-07 16:37:04', 3, 3),
(29, 'Shaggy High-Pile Carpet', 'Thick, plush surface for cozy comfort underfoot in bedrooms or lounges.', '200 x 300 x 3 cm', '2025-11-07 16:38:24', 5, 4),
(30, 'Geometric Pattern Runner', 'Bold shapes and elongated form perfect for hallways or narrow spaces.', '60 x 300 x 1 cm', '2025-11-07 16:39:13', 4, 4),
(31, 'Washable Kids Play Mat', 'Soft, safe, and easy to clean—ideal for playrooms and nurseries.', '150 x 200 x 1 cm', '2025-11-07 16:39:49', 5, 4),
(32, 'Persian-Style Area Rug', 'Intricate patterns and rich colors add warmth and elegance to large spaces', '240 x 340 x 1 cm', '2025-11-08 14:46:16', 5, 4);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `img_path`, `alt_text`, `product_id`) VALUES
(4, '/Furnitures/admin/product_images/images/IMG_6297.JPG', '', 1),
(5, '/Furnitures/admin/product_images/images/IMG_6298.JPG', '', 1),
(6, '/Furnitures/admin/product_images/images/IMG_6281.JPG', 'sectional-sofa', 2),
(7, '/Furnitures/admin/product_images/images/IMG_6282.JPG', '', 2),
(8, '/Furnitures/admin/product_images/images/IMG_6280.JPG', '', 2),
(9, '/Furnitures/admin/product_images/images/IMG_6309.JPG', '', 3),
(10, '/Furnitures/admin/product_images/images/IMG_6310.JPG', '', 3),
(11, '/Furnitures/admin/product_images/images/IMG_6311.JPG', '', 3),
(12, '/Furnitures/admin/product_images/images/IMG_6374.JPG', '', 4),
(13, '/Furnitures/admin/product_images/images/IMG_6375.JPG', '', 4),
(14, '/Furnitures/admin/product_images/images/IMG_6376.JPG', '', 4),
(16, '/Furnitures/admin/product_images/images/IMG_6388.JPG', '', 5),
(17, '/Furnitures/admin/product_images/images/IMG_6389.JPG', '', 5),
(18, '/Furnitures/admin/product_images/images/IMG_6390.JPG', '', 5),
(22, '/Furnitures/admin/product_images/images/IMG_6283.JPG', '', 7),
(23, '/Furnitures/admin/product_images/images/IMG_6284.JPG', '', 7),
(24, '/Furnitures/admin/product_images/images/IMG_6285.JPG', '', 7),
(25, '/Furnitures/admin/product_images/images/IMG_6286.JPG', '', 8),
(26, '/Furnitures/admin/product_images/images/IMG_6287.JPG', '', 8),
(27, '/Furnitures/admin/product_images/images/IMG_6288.JPG', '', 8),
(29, '/Furnitures/admin/product_images/images/IMG_6289.JPG', '', 9),
(30, '/Furnitures/admin/product_images/images/IMG_6290.JPG', '', 9),
(31, '/Furnitures/admin/product_images/images/IMG_6291.JPG', '', 9),
(32, '/Furnitures/admin/product_images/images/IMG_6292.JPG', '', 11),
(33, '/Furnitures/admin/product_images/images/IMG_6293.JPG', '', 11),
(34, '/Furnitures/admin/product_images/images/IMG_6294.JPG', '', 11),
(35, '/Furnitures/admin/product_images/images/IMG_6299.JPG', '', 12),
(36, '/Furnitures/admin/product_images/images/IMG_6300.JPG', '', 12),
(37, '/Furnitures/admin/product_images/images/IMG_6301.JPG', '', 12),
(38, '/Furnitures/admin/product_images/images/IMG_6302.JPG', '', 13),
(39, '/Furnitures/admin/product_images/images/IMG_6303.JPG', '', 13),
(40, '/Furnitures/admin/product_images/images/IMG_6304.JPG', '', 14),
(41, '/Furnitures/admin/product_images/images/IMG_6305.JPG', '', 14),
(42, '/Furnitures/admin/product_images/images/IMG_6306.JPG', '', 14),
(43, '/Furnitures/admin/product_images/images/IMG_6307.JPG', '', 15),
(44, '/Furnitures/admin/product_images/images/IMG_6308.JPG', '', 15),
(46, '/Furnitures/admin/product_images/images/IMG_6313.JPG', 'ab', 16),
(47, '/Furnitures/admin/product_images/images/IMG_6314.JPG', '', 16),
(48, '/Furnitures/admin/product_images/images/IMG_6315.JPG', '', 17),
(49, '/Furnitures/admin/product_images/images/IMG_6316.JPG', '', 17),
(50, '/Furnitures/admin/product_images/images/IMG_6317.JPG', '', 17),
(51, '/Furnitures/admin/product_images/images/IMG_6318.JPG', '', 18),
(52, '/Furnitures/admin/product_images/images/IMG_6319.JPG', '', 18),
(53, '/Furnitures/admin/product_images/images/IMG_6320.JPG', '', 18),
(54, '/Furnitures/admin/product_images/images/IMG_6321.JPG', '', 19),
(55, '/Furnitures/admin/product_images/images/IMG_6322.JPG', '', 19),
(56, '/Furnitures/admin/product_images/images/IMG_6323.JPG', '', 19),
(57, '/Furnitures/admin/product_images/images/IMG_6377.JPG', '', 20),
(58, '/Furnitures/admin/product_images/images/IMG_6378.JPG', '', 20),
(59, '/Furnitures/admin/product_images/images/IMG_6379.JPG', '', 20),
(60, '/Furnitures/admin/product_images/images/IMG_6380.JPG', '', 21),
(61, '/Furnitures/admin/product_images/images/IMG_6381.JPG', '', 21),
(62, '/Furnitures/admin/product_images/images/IMG_6382.JPG', '', 22),
(63, '/Furnitures/admin/product_images/images/IMG_6383.JPG', '', 22),
(64, '/Furnitures/admin/product_images/images/IMG_6384.JPG', '', 22),
(65, '/Furnitures/admin/product_images/images/IMG_6385.JPG', '', 23),
(66, '/Furnitures/admin/product_images/images/IMG_6386.JPG', '', 23),
(67, '/Furnitures/admin/product_images/images/IMG_6387.JPG', '', 23),
(68, '/Furnitures/admin/product_images/images/IMG_6391.JPG', '', 24),
(69, '/Furnitures/admin/product_images/images/IMG_6392.JPG', '', 24),
(70, '/Furnitures/admin/product_images/images/IMG_6393.JPG', '', 25),
(71, '/Furnitures/admin/product_images/images/IMG_6394.JPG', '', 25),
(72, '/Furnitures/admin/product_images/images/IMG_6395.JPG', '', 25),
(73, '/Furnitures/admin/product_images/images/IMG_6396.JPG', '', 26),
(74, '/Furnitures/admin/product_images/images/IMG_6397.JPG', '', 26),
(75, '/Furnitures/admin/product_images/images/IMG_6398.JPG', '', 26),
(76, '/Furnitures/admin/product_images/images/IMG_6399.JPG', '', 27),
(77, '/Furnitures/admin/product_images/images/IMG_6400.JPG', '', 27),
(78, '/Furnitures/admin/product_images/images/IMG_6401.JPG', '', 27),
(79, '/Furnitures/admin/product_images/images/IMG_6402.JPG', '', 32),
(80, '/Furnitures/admin/product_images/images/IMG_6403.JPG', '', 32),
(81, '/Furnitures/admin/product_images/images/IMG_6404.JPG', '', 32),
(82, '/Furnitures/admin/product_images/images/IMG_6405.JPG', '', 6),
(83, '/Furnitures/admin/product_images/images/IMG_6406.JPG', '', 6),
(84, '/Furnitures/admin/product_images/images/IMG_6407.JPG', '', 29),
(85, '/Furnitures/admin/product_images/images/IMG_6408.JPG', '', 29),
(86, '/Furnitures/admin/product_images/images/IMG_6409.JPG', '', 29),
(87, '/Furnitures/admin/product_images/images/IMG_6410.JPG', '', 30),
(88, '/Furnitures/admin/product_images/images/IMG_6411.JPG', '', 30),
(89, '/Furnitures/admin/product_images/images/IMG_6412.JPG', '', 30),
(90, '/Furnitures/admin/product_images/images/IMG_6413.JPG', '', 31),
(91, '/Furnitures/admin/product_images/images/IMG_6414.JPG', '', 31),
(92, '/Furnitures/admin/product_images/images/IMG_6415.JPG', '', 31),
(94, '/Furnitures/admin/product_images/images/IMG_6312.JPG', 'aa-chair', 16),
(96, '/Furnitures/admin/product_images/images/IMG_6296.JPG', 'aa', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_tags`
--

CREATE TABLE `product_tags` (
  `product_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_tags`
--

INSERT INTO `product_tags` (`product_id`, `tag_id`) VALUES
(1, 7),
(1, 38),
(2, 2),
(2, 9),
(3, 3),
(3, 29),
(3, 31),
(4, 4),
(4, 28),
(4, 34),
(5, 7),
(6, 6),
(6, 35),
(7, 2),
(7, 8),
(8, 2),
(8, 7),
(8, 9),
(9, 2),
(9, 10),
(9, 32),
(11, 2),
(11, 11),
(14, 12),
(14, 20),
(15, 13),
(16, 3),
(16, 30),
(16, 33),
(17, 3),
(17, 15),
(18, 3),
(18, 28),
(19, 3),
(20, 4),
(20, 7),
(20, 17),
(21, 4),
(21, 7),
(21, 18),
(22, 4),
(22, 19),
(23, 4),
(23, 20),
(24, 21),
(25, 33),
(26, 22),
(27, 23),
(29, 6),
(29, 24),
(29, 36),
(30, 6),
(30, 25),
(31, 6),
(31, 26),
(31, 37),
(32, 6),
(32, 27);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL,
  `color` varchar(45) DEFAULT NULL,
  `material` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `color`, `material`, `price`, `product_id`) VALUES
(4, 'blue', 'metal', 8998.00, 1),
(5, 'brown', 'metal', 8998.00, 1),
(6, 'black', 'plastic', 1999.00, 4),
(7, 'black', 'wood', 7999.00, 4),
(8, 'beige', 'linen', 8999.00, 2),
(9, 'blue', 'linen', 8999.00, 2),
(10, 'white', '', 6999.00, 3),
(11, 'black', '', 6999.00, 3),
(12, 'white', 'wood', 9999.00, 4),
(13, 'white', 'wood', 6999.00, 5),
(15, 'pink', '', 799.00, 6),
(16, 'red', '', 799.00, 6),
(17, 'brown', 'velvet', 8999.00, 7),
(18, 'red', 'leather', 12999.00, 7),
(19, 'beige', 'leather', 9999.00, 8),
(20, 'green', 'linen', 12999.00, 8),
(21, 'pink', 'velvet', 8999.00, 9),
(22, 'red', 'leather', 8999.00, 9),
(23, 'purple', 'leather', 10999.00, 11),
(24, 'brown', 'leather', 10999.00, 11),
(25, 'brown', 'wood', 9999.00, 12),
(26, 'white', 'metal', 12999.00, 12),
(27, 'brown', 'wood', 6999.00, 13),
(28, 'white', 'wood', 9999.00, 13),
(29, 'brown', 'wood', 6999.00, 14),
(30, 'black', 'metal', 9999.00, 14),
(31, 'beige', 'wood', 8999.00, 15),
(32, 'red', 'wood', 6999.00, 15),
(35, 'red', 'wood', 9999.00, 17),
(36, 'pink', 'wood', 6999.00, 17),
(37, 'brown', 'wood', 6999.00, 18),
(38, 'black', 'metal', 8999.00, 18),
(39, 'white', 'wood', 6999.00, 19),
(40, 'pink', 'wood', 8999.00, 19),
(41, 'brown', 'wood', 9999.00, 20),
(42, 'red', 'wood', 8999.00, 20),
(43, 'brown', 'wood', 9999.00, 21),
(44, 'beige', 'wood', 6999.00, 21),
(45, 'white', 'wood', 9999.00, 22),
(46, 'black', 'wood', 9999.00, 22),
(47, 'black', '', 8999.00, 23),
(48, 'white', '', 8999.00, 23),
(49, 'brown', 'wood', 9999.00, 24),
(50, 'pink', 'wood', 8999.00, 24),
(51, 'white', 'wood', 11999.00, 25),
(52, 'red', 'wood', 11999.00, 25),
(53, 'red', 'wood', 7999.00, 26),
(54, 'black', 'wood', 6999.00, 26),
(55, 'red', 'wood', 6999.00, 27),
(56, 'brown', 'wood', 6999.00, 27),
(59, 'black', '', 1299.00, 29),
(60, 'blue', '', 1299.00, 29),
(61, 'green', '', 1099.00, 30),
(62, 'red', '', 1099.00, 30),
(63, 'pink', '', 899.00, 31),
(64, 'purple', '', 899.00, 31),
(65, 'pink', '', 999.00, 32),
(66, 'red', '', 999.00, 32),
(70, 'white', '', 6999.00, 16),
(77, 'beige', 'wood', 10999.00, 1),
(79, 'pink', '', 6999.00, 16);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `product_id`, `variant_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(3, 1, 3, 10, 5, 'maganda', '2025-11-13 15:28:44', '2025-11-13 15:42:38'),
(4, 1, 1, 4, 5, 'matibay **** **** grabe', '2025-11-13 15:55:12', '2025-11-15 18:22:15'),
(5, 1, 2, 8, 5, 'maganda', '2025-11-14 16:23:25', NULL),
(6, 19, 1, 4, 2, '', '2025-11-15 14:37:43', '2025-11-15 14:41:20'),
(7, 4, 12, 25, 5, 'sobrang ganda *******', '2025-11-15 18:25:27', NULL),
(8, 23, 1, 4, 4, '******* ganda', '2025-11-16 21:51:49', '2025-11-16 22:12:57');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`variant_id`, `quantity`) VALUES
(4, 57),
(5, 58),
(6, 0),
(7, 0),
(8, 86),
(9, 42),
(10, 97),
(11, 93),
(12, 0),
(13, 65),
(15, 56),
(16, 93),
(17, 69),
(18, 93),
(19, 44),
(20, 93),
(21, 77),
(22, 56),
(23, 57),
(24, 96),
(25, 91),
(26, 46),
(27, 93),
(28, 63),
(29, 45),
(30, 63),
(31, 70),
(32, 46),
(35, 70),
(36, 93),
(37, 93),
(38, 70),
(39, 46),
(40, 96),
(41, 63),
(42, 70),
(43, 46),
(44, 63),
(45, 93),
(46, 96),
(47, 93),
(48, 60),
(49, 93),
(50, 63),
(51, 63),
(52, 93),
(53, 70),
(54, 96),
(55, 46),
(56, 93),
(59, 38),
(60, 70),
(61, 69),
(62, 92),
(63, 45),
(64, 96),
(65, 58),
(66, 23),
(70, 48),
(77, 54),
(79, 93);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `name`, `created_at`) VALUES
(2, 'sofa', '2025-11-12 13:54:19'),
(3, 'chair', '2025-11-12 13:54:19'),
(4, 'table', '2025-11-12 13:54:19'),
(6, 'rug', '2025-11-12 13:54:19'),
(7, 'storage', '2025-11-12 13:54:19'),
(8, 'recliner', '2025-11-12 13:54:19'),
(9, 'sleeper', '2025-11-12 13:54:19'),
(10, 'loveseat', '2025-11-12 13:54:19'),
(11, 'mid-century', '2025-11-12 13:54:19'),
(12, 'bunk', '2025-11-12 13:54:19'),
(13, 'canopy', '2025-11-12 13:54:19'),
(15, 'rocking', '2025-11-12 13:54:19'),
(16, 'foldable', '2025-11-12 13:54:19'),
(17, 'coffee', '2025-11-12 13:54:19'),
(18, 'console', '2025-11-12 13:54:19'),
(19, 'side', '2025-11-12 13:54:19'),
(20, 'desk', '2025-11-12 13:54:19'),
(21, 'tv', '2025-11-12 13:54:19'),
(22, 'shoe', '2025-11-12 13:54:19'),
(23, 'modular', '2025-11-12 13:54:19'),
(24, 'carpet', '2025-11-12 13:54:19'),
(25, 'runner', '2025-11-12 13:54:19'),
(26, 'playmat', '2025-11-12 13:54:19'),
(27, 'persian', '2025-11-12 13:54:19'),
(28, 'dining', '2025-11-12 13:54:19'),
(29, 'office', '2025-11-12 13:54:19'),
(30, 'linen', '2025-11-12 13:54:19'),
(31, 'mesh', '2025-11-12 13:54:19'),
(32, 'tufted', '2025-11-12 13:54:19'),
(33, 'display', '2025-11-12 13:54:19'),
(34, 'extendable', '2025-11-12 13:54:19'),
(35, 'jute', '2025-11-12 13:54:19'),
(36, 'high-pile', '2025-11-12 13:54:19'),
(37, 'washable', '2025-11-12 13:54:19'),
(38, 'bed', '2025-11-15 19:58:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `role` varchar(10) NOT NULL DEFAULT 'customer',
  `img_path` varchar(255) DEFAULT NULL,
  `is_deleted` int(11) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `first_name`, `last_name`, `is_active`, `created_at`, `role`, `img_path`, `is_deleted`, `deleted_at`) VALUES
(1, 'jerichopbellen@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Jericho', 'Bellen', 1, '2025-11-01 17:13:58', 'admin', '/Furnitures/user/avatars/default-avatar.png', 0, NULL),
(4, 'deleted_4@example.com', '850b36862fd1c00860ed3a968e75dc110eb9bcf6', 'Deleted', 'User', 0, '2025-11-02 18:05:08', 'customer', '/Furnitures/user/avatars/default-avatar.png', 1, '2025-11-15 20:13:05'),
(11, 'arianagrande@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Ariana', 'Grande', 1, '2025-11-02 18:41:44', 'customer', '', 0, NULL),
(12, 'deleted_12@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Deleted', 'User', 0, '2025-11-02 18:45:39', 'customer', '', 1, '2025-11-10 13:39:10'),
(17, 'deleted_17@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Deleted', 'User', 0, '2025-11-02 18:54:02', 'customer', '', 1, '2025-11-10 13:37:10'),
(18, 'deleted_18@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Deleted', 'User', 0, '2025-11-02 18:58:11', 'customer', '', 1, '2025-11-10 13:32:38'),
(19, 'lisa@gmail.com', '850b36862fd1c00860ed3a968e75dc110eb9bcf6', 'Lisa', 'Manoban', 1, '2025-11-12 05:54:36', 'customer', '/Furnitures/user/avatars/top-view-soft-drink-glass-with-ice-cubes-straw.jpg', 0, NULL),
(20, 'jisoo@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'jisoo', 'kim', 1, '2025-11-14 01:06:50', 'customer', '', 0, NULL),
(21, 'deleted_21@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Deleted', 'User', 0, '2025-11-14 01:27:15', 'customer', '/Furnitures/user/avatars/profile_691614f2f08bc5.39360019.jpg', 1, '2025-11-15 20:22:59'),
(23, 'taylorswift@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'taylor', 'swift', 1, '2025-11-16 21:40:02', 'customer', '/Furnitures/user/avatars/6919d455b652e_top-view-soft-drink-glass-with-ice-cubes-straw.jpg', 0, NULL),
(24, 'olivia@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'olivia', 'rodrigo', 1, '2025-11-17 01:45:58', 'customer', '/Furnitures/user/avatars/default-avatar.png', 0, NULL),
(25, 'sabrinacarpenter@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'sabrina', 'carpenter', 1, '2025-11-17 02:00:08', 'customer', '/Furnitures/user/avatars/profile_691a1127f0a626.90149803.jpg', 0, NULL),
(26, 'xeanalexandra.ladignon@depedparanaquecity.com', '0577faaa110ded5c9b472a4df7493117fd07add9', 'Xean', 'Ladignon', 1, '2025-11-18 10:40:08', 'customer', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_order_transaction_details`
-- (See below for the actual view)
--
CREATE TABLE `view_order_transaction_details` (
`order_id` int(11)
,`created_at` timestamp
,`status` enum('Pending','Processing','Shipped','Delivered','Cancelled','Received')
,`tracking_number` varchar(100)
,`user_id` int(11)
,`customer_name` varchar(511)
,`customer_email` varchar(255)
,`address_id` int(11)
,`recipient` varchar(255)
,`street` varchar(255)
,`barangay` varchar(255)
,`city` varchar(255)
,`province` varchar(255)
,`zipcode` varchar(45)
,`country` varchar(100)
,`phone` varchar(45)
,`product_name` varchar(255)
,`brand_name` varchar(45)
,`category_name` varchar(100)
,`variant_id` int(11)
,`color` varchar(45)
,`material` varchar(255)
,`unit_price` decimal(10,2)
,`quantity` int(11)
,`subtotal` decimal(20,2)
);

-- --------------------------------------------------------

--
-- Structure for view `view_order_transaction_details`
--
DROP TABLE IF EXISTS `view_order_transaction_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_order_transaction_details`  AS SELECT `o`.`order_id` AS `order_id`, `o`.`created_at` AS `created_at`, `o`.`status` AS `status`, `o`.`tracking_number` AS `tracking_number`, `u`.`user_id` AS `user_id`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `customer_name`, `u`.`email` AS `customer_email`, `o`.`address_id` AS `address_id`, `a`.`recipient` AS `recipient`, `a`.`street` AS `street`, `a`.`barangay` AS `barangay`, `a`.`city` AS `city`, `a`.`province` AS `province`, `a`.`zipcode` AS `zipcode`, `a`.`country` AS `country`, `a`.`phone` AS `phone`, `p`.`name` AS `product_name`, `b`.`name` AS `brand_name`, `c`.`name` AS `category_name`, `v`.`variant_id` AS `variant_id`, `v`.`color` AS `color`, `v`.`material` AS `material`, `v`.`price` AS `unit_price`, `oi`.`quantity` AS `quantity`, `v`.`price`* `oi`.`quantity` AS `subtotal` FROM (((((((`orders` `o` join `users` `u` on(`o`.`user_id` = `u`.`user_id`)) join `addresses` `a` on(`o`.`address_id` = `a`.`address_id`)) join `order_items` `oi` on(`o`.`order_id` = `oi`.`order_id`)) join `product_variants` `v` on(`oi`.`variant_id` = `v`.`variant_id`)) join `products` `p` on(`v`.`product_id` = `p`.`product_id`)) join `brands` `b` on(`p`.`brand_id` = `b`.`brand_id`)) join `categories` `c` on(`p`.`category_id` = `c`.`category_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`) USING BTREE,
  ADD KEY `fk_addresses_users1_idx` (`user_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD UNIQUE KEY `unique_user_variant` (`user_id`,`variant_id`),
  ADD KEY `fk_users_has_product_variants_product_variants1_idx` (`variant_id`),
  ADD KEY `fk_users_has_product_variants_users1_idx` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `unique_category_name` (`name`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`) USING BTREE,
  ADD UNIQUE KEY `tracking_number_UNIQUE` (`tracking_number`),
  ADD KEY `fk_orders_users_idx` (`user_id`),
  ADD KEY `fk_orders_address` (`address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_items_id`),
  ADD KEY `fk_orders_has_product_variants_product_variants1_idx` (`variant_id`),
  ADD KEY `fk_orders_has_product_variants_orders1_idx` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`) USING BTREE,
  ADD KEY `fk_products_brands1_idx` (`brand_id`),
  ADD KEY `fk_products_categories1_idx` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`) USING BTREE,
  ADD KEY `fk_product_images_products1_idx` (`product_id`);

--
-- Indexes for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD PRIMARY KEY (`product_id`,`tag_id`),
  ADD KEY `product_tags_ibfk_2` (`tag_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`) USING BTREE,
  ADD KEY `fk_product_variants_products1_idx` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `fk_stocks_product_variants1_idx` (`variant_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_items_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `fk_addresses_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_users_has_product_variants_product_variants1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_has_product_variants_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_orders_has_product_variants_orders1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_orders_has_product_variants_product_variants1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_brands1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_products_categories1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_products1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD CONSTRAINT `product_tags_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE NO ACTION;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_product_variants_products1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `fk_stocks_product_variants1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

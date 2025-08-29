-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2025 at 01:25 PM
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
-- Database: `lap_canteen`
--

-- --------------------------------------------------------

--
-- Table structure for table `manu_items`
--

CREATE TABLE `manu_items` (
  `ItemID` varchar(20) NOT NULL,
  `ItemName` varchar(20) NOT NULL,
  `Category` varchar(20) NOT NULL,
  `Price` int(11) NOT NULL,
  `Status` tinyint(4) DEFAULT NULL,
  `Descriptions` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manu_items`
--

INSERT INTO `manu_items` (`ItemID`, `ItemName`, `Category`, `Price`, `Status`, `Descriptions`) VALUES
('B01', 'Strawberry Juice', 'Bevarages', 3000, 1, NULL),
('B02', 'Orange Juice', 'Bevarages', 3000, 1, NULL),
('B03', 'Mango Juice', 'Bevarages', 3000, 1, NULL),
('B04', 'Lemon Juice', 'Bevarages', 3000, 1, NULL),
('B05', 'Iced coffee', 'Bevarages', 2500, 1, NULL),
('B06', 'Watermelon Juice', 'Bevarages', 3000, 1, NULL),
('B07', 'Avocado smoothie', 'Bevarages', 4500, 1, NULL),
('B08', 'Berry bliss smoothie', 'Bevarages', 5000, 1, NULL),
('B09', 'Papaya Juice', 'Bevarages', 3500, 1, NULL),
('B10', 'Passion fruit Juice', 'bevarages', 3500, 1, NULL),
('B11', 'Pineapple Juice', 'Bevarages', 3000, 1, NULL),
('B12', 'Grape Juice', 'Bevarages', 3000, 1, NULL),
('D01', 'Shwe yin aye', 'Dessert', 2800, 1, NULL),
('D02', 'Mont lat saung', 'Dessert', 5000, 1, NULL),
('D03', 'Mango', 'Dessert', 2000, 1, NULL),
('D04', 'Strawberry', 'Dessert', 2000, 1, NULL),
('D05', 'Dragon fruit', 'Dessert', 2000, 1, NULL),
('D06', 'Orange', 'Dessert', 2000, 1, NULL),
('D07', 'Triple chocolate cak', 'Dessert', 4000, 1, NULL),
('D08', 'strawberry Icecream', 'Dessert', 5000, 1, NULL),
('D09', 'Chocolate sundae', 'Dessert', 2000, 1, NULL),
('D10', 'Mini lemon chesscake', 'Dessert', 3000, 1, NULL),
('D11', 'Coconut Jelly', 'Dessert', 3000, 1, NULL),
('D12', 'Cake ball', 'Dessert', 2500, 1, NULL),
('MC01', 'Mohinga', 'Maincourse', 5000, 1, NULL),
('MC02', 'Nangyithoke', 'Maincourse', 5000, 1, NULL),
('MC03', 'Tofu Nway Noodle', 'Maincourse', 6000, 1, NULL),
('MC04', 'Shan Noodle', 'Maincourse', 6000, 1, NULL),
('MC05', 'Green Tea leaf rice', 'Maincourse', 5000, 1, NULL),
('MC06', 'Seafood fried rice', 'Maincourse', 7000, 1, NULL),
('MC07', 'Kyay OH', 'Maincourse', 6000, 1, NULL),
('MC08', 'Fried Noodle', 'Maincourse', 6000, 1, NULL),
('MC09', 'Mala Xiang gao', 'Maincourse', 9500, 1, NULL),
('MC10', 'Omelet rice', 'Maincourse', 3000, 1, NULL),
('MC11', 'Vermicelli Soup', 'Maincourse', 2000, 1, NULL),
('MC12', 'Hta Ma Nae', 'Maincourse', 5000, 1, NULL),
('TS01', 'fried vegetable', 'Traditionsnack', 9000, 1, NULL),
('TS02', 'Tea leaf salad', 'Traditionsnack', 5500, 1, NULL),
('TS03', 'Mont lin mayar', 'Traditonalsnack', 3000, 1, NULL),
('TS04', 'Mont let kauk', 'Traditionalsnack', 2000, 1, NULL),
('TS05', 'Fried Tofu', 'Traditionalsnack', 3500, 1, NULL),
('TS06', 'Fried samuzar', 'Traditionalsnack', 2000, 1, NULL),
('TS07', 'Mont si kyaw', 'Traditionalsnack', 2000, 1, NULL),
('TS08', 'Plantain chip', 'Traditionalsnack', 5000, 1, NULL),
('TS09', 'Kout nyin kyi tout', 'Traditionalsnack', 2000, 1, NULL),
('TS10', 'Thingyan snack', 'Traditionalsnack', 2000, 1, NULL),
('TS11', 'Halwa', 'Traditionalsnack', 2000, 1, NULL),
('Ts12', 'Shan snacks', 'Traditionalsnack', 2000, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `OrderDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ItemID` varchar(10) NOT NULL,
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `Email` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `manu_items`
--
ALTER TABLE `manu_items`
  ADD PRIMARY KEY (`ItemID`),
  ADD UNIQUE KEY `ItemName` (`ItemName`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`OrderID`,`UserID`),
  ADD UNIQUE KEY `OrderID` (`OrderID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ItemID` (`ItemID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `ItemID` FOREIGN KEY (`ItemID`) REFERENCES `manu_items` (`ItemID`),
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

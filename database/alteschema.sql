-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: db5004652185.hosting-data.io
-- Erstellungszeit: 30. Dez 2025 um 16:46
-- Server-Version: 5.7.42-log
-- PHP-Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `dbs3895544`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `addressbook`
--

CREATE TABLE `addressbook` (
  `MYID` bigint(21) NOT NULL,
  `PRINT_NAME` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `PREFIX` varchar(30) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `FIRSTNAME` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `LASTNAME` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TITLE` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `DEPARTMENT` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `ADDRESS` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `CITY` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `STATEPROV` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `POSTALCODE` varchar(20) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COUNTRY` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `POSITION` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `INITIALS` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `SALUTATION` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `PHONEHOME` varchar(40) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `PHONEOFFI` varchar(40) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `PHONEOTHE` varchar(40) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `PHONEWORK` varchar(40) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `MOBILE` varchar(40) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `PAGER` varchar(40) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `FAX` varchar(40) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `EMAIL` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `EMAIL2` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `URL` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `URL2` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `NOTE` mediumtext COLLATE latin1_german2_ci NOT NULL,
  `CHANGELOG` mediumtext COLLATE latin1_german2_ci NOT NULL,
  `ALTFIELD1` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `ALTFIELD2` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `CATEGORY` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `METHODOFPAY` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `MESSAGE` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `BIRTHDAY` date NOT NULL DEFAULT '0000-00-00',
  `BANKNAME` varchar(200) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `BANKACCOUNT` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `BANKNUMBER` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `BANKIBAN` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `BANKBIC` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX_FREE` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `TAXNR` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `BUSINESS_TAXNR` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `USERNAME` blob NOT NULL,
  `PASSWORD` blob NOT NULL,
  `USERLANGUAGE` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `USER_ACTIVE` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `article`
--

CREATE TABLE `article` (
  `POSITIONID` bigint(21) UNSIGNED NOT NULL,
  `POS_NAME` varchar(100) COLLATE latin1_german2_ci NOT NULL,
  `POS_DESC` text COLLATE latin1_german2_ci NOT NULL,
  `POSGROUPID` smallint(5) UNSIGNED NOT NULL,
  `POS_GROUP` varchar(150) COLLATE latin1_german2_ci NOT NULL,
  `POS_PRICE` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_TAX` tinyint(3) UNSIGNED NOT NULL,
  `POS_ACTIVE` tinyint(3) UNSIGNED NOT NULL,
  `NOTE` text COLLATE latin1_german2_ci NOT NULL,
  `POS_INVENTORY` int(11) UNSIGNED NOT NULL,
  `POS_INVENTORY_CURRENT` int(11) UNSIGNED NOT NULL,
  `POS_INVENTORY_PURCHASING` int(11) UNSIGNED NOT NULL,
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cashbook`
--

CREATE TABLE `cashbook` (
  `CASHBOOKID` bigint(21) UNSIGNED NOT NULL,
  `MYID` bigint(21) UNSIGNED NOT NULL,
  `INVOICEID` bigint(21) UNSIGNED NOT NULL,
  `PAYMENTID` bigint(21) NOT NULL,
  `DESCRIPTION` varchar(150) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `CASHBOOK_DATE` date NOT NULL DEFAULT '0000-00-00',
  `TAKINGS` decimal(21,2) NOT NULL DEFAULT '0.00',
  `EXPENDITURES` decimal(21,2) NOT NULL DEFAULT '0.00',
  `CASH_IN_HAND` decimal(21,2) NOT NULL DEFAULT '0.00',
  `CASH_IN_HAND_STARTING_WITH` decimal(21,2) NOT NULL DEFAULT '0.00',
  `CANCELED` tinyint(1) UNSIGNED NOT NULL,
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `category`
--

CREATE TABLE `category` (
  `CATEGORYID` smallint(5) UNSIGNED NOT NULL,
  `DESCRIPTION` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `customerpos`
--

CREATE TABLE `customerpos` (
  `CUSTOMERPOSID` bigint(21) UNSIGNED NOT NULL,
  `MYID` bigint(21) UNSIGNED NOT NULL,
  `POSITIONID` bigint(21) UNSIGNED NOT NULL,
  `POS_DESC` text COLLATE latin1_german2_ci NOT NULL,
  `POS_QUANTITY` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_PRICE` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_GROUP` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX` tinyint(3) UNSIGNED NOT NULL,
  `TAX_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX_MULTI` decimal(6,5) NOT NULL DEFAULT '0.00000',
  `TAX_DIVIDE` decimal(6,5) NOT NULL DEFAULT '0.00000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invoice`
--

CREATE TABLE `invoice` (
  `INVOICEID` bigint(21) UNSIGNED NOT NULL,
  `MYID` bigint(21) UNSIGNED NOT NULL,
  `INVOICE_DATE` date NOT NULL DEFAULT '0000-00-00',
  `MESSAGEID` smallint(5) UNSIGNED NOT NULL,
  `MESSAGE_DESC` text COLLATE latin1_german2_ci NOT NULL,
  `METHODOFPAYID` tinyint(3) UNSIGNED NOT NULL,
  `METHOD_OF_PAY` varchar(150) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `METHOD_OF_PAY_DATE` date NOT NULL DEFAULT '0000-00-00',
  `TAX1_TOTAL` decimal(21,2) NOT NULL DEFAULT '0.00',
  `TAX2_TOTAL` decimal(21,2) NOT NULL DEFAULT '0.00',
  `TAX3_TOTAL` decimal(21,2) NOT NULL DEFAULT '0.00',
  `TAX4_TOTAL` decimal(21,2) NOT NULL DEFAULT '0.00',
  `TAX1_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX2_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX3_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX4_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `SUBTOTAL1` decimal(21,2) NOT NULL DEFAULT '0.00',
  `SUBTOTAL2` decimal(21,2) NOT NULL DEFAULT '0.00',
  `SUBTOTAL3` decimal(21,2) NOT NULL DEFAULT '0.00',
  `SUBTOTAL4` decimal(21,2) NOT NULL DEFAULT '0.00',
  `TOTAL_AMOUNT` decimal(21,2) NOT NULL DEFAULT '0.00',
  `NOTE` text COLLATE latin1_german2_ci NOT NULL,
  `PAID` tinyint(3) UNSIGNED NOT NULL,
  `SUM_PAID` decimal(21,2) NOT NULL DEFAULT '0.00',
  `DELIVERY_NOTE_PRINTED` tinyint(3) UNSIGNED NOT NULL,
  `DELIVERY_NOTE_MAILED` tinyint(3) UNSIGNED NOT NULL,
  `INVOICE_PRINTED` tinyint(3) UNSIGNED NOT NULL,
  `INVOICE_MAILED` tinyint(3) UNSIGNED NOT NULL,
  `CANCELED` tinyint(3) UNSIGNED NOT NULL,
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ACHIEVED_DATE` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invoicepos`
--

CREATE TABLE `invoicepos` (
  `INVOICEPOSID` bigint(21) UNSIGNED NOT NULL,
  `MYID` bigint(21) UNSIGNED NOT NULL,
  `INVOICEID` bigint(21) UNSIGNED NOT NULL,
  `POSITIONID` bigint(21) UNSIGNED NOT NULL,
  `POS_DESC` text COLLATE latin1_german2_ci NOT NULL,
  `POS_QUANTITY` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_PRICE` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_GROUP` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX` tinyint(3) UNSIGNED NOT NULL,
  `TAX_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX_MULTI` decimal(6,5) NOT NULL DEFAULT '0.00000',
  `TAX_DIVIDE` decimal(6,5) NOT NULL DEFAULT '0.00000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message`
--

CREATE TABLE `message` (
  `MESSAGEID` smallint(5) UNSIGNED NOT NULL,
  `DESCRIPTION` text CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci NOT NULL,
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `methodofpay`
--

CREATE TABLE `methodofpay` (
  `METHODOFPAYID` tinyint(3) UNSIGNED NOT NULL,
  `DESCRIPTION` varchar(150) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `offer`
--

CREATE TABLE `offer` (
  `OFFERID` bigint(21) UNSIGNED NOT NULL,
  `MYID` bigint(21) UNSIGNED NOT NULL,
  `INVOICEID` bigint(21) UNSIGNED NOT NULL,
  `OFFER_DATE` date NOT NULL DEFAULT '0000-00-00',
  `MESSAGEID` smallint(5) UNSIGNED NOT NULL,
  `MESSAGE_DESC` text COLLATE latin1_german2_ci NOT NULL,
  `METHODOFPAYID` tinyint(3) UNSIGNED NOT NULL,
  `METHOD_OF_PAY` varchar(150) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `METHOD_OF_PAY_DATE` date NOT NULL DEFAULT '0000-00-00',
  `STATUS` tinyint(3) NOT NULL DEFAULT '1',
  `TAX1_TOTAL` decimal(21,2) NOT NULL DEFAULT '0.00',
  `TAX2_TOTAL` decimal(21,2) NOT NULL DEFAULT '0.00',
  `TAX3_TOTAL` decimal(21,2) NOT NULL DEFAULT '0.00',
  `TAX4_TOTAL` decimal(21,2) NOT NULL DEFAULT '0.00',
  `TAX1_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX2_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX3_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX4_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `SUBTOTAL1` decimal(21,2) NOT NULL DEFAULT '0.00',
  `SUBTOTAL2` decimal(21,2) NOT NULL DEFAULT '0.00',
  `SUBTOTAL3` decimal(21,2) NOT NULL DEFAULT '0.00',
  `SUBTOTAL4` decimal(21,2) NOT NULL DEFAULT '0.00',
  `TOTAL_AMOUNT` decimal(21,2) NOT NULL DEFAULT '0.00',
  `NOTE` text COLLATE latin1_german2_ci NOT NULL,
  `ORDER_PRINTED` tinyint(3) UNSIGNED NOT NULL,
  `ORDER_MAILED` tinyint(3) UNSIGNED NOT NULL,
  `OFFER_PRINTED` tinyint(3) UNSIGNED NOT NULL,
  `OFFER_MAILED` tinyint(3) UNSIGNED NOT NULL,
  `CANCELED` tinyint(3) UNSIGNED NOT NULL,
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `offerpos`
--

CREATE TABLE `offerpos` (
  `OFFERPOSID` bigint(21) UNSIGNED NOT NULL,
  `OFFERID` bigint(21) UNSIGNED NOT NULL,
  `MYID` bigint(21) UNSIGNED NOT NULL,
  `POSITIONID` bigint(21) UNSIGNED NOT NULL,
  `POS_DESC` text COLLATE latin1_german2_ci NOT NULL,
  `POS_QUANTITY` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_PRICE` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_GROUP` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX` tinyint(3) UNSIGNED NOT NULL,
  `TAX_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX_MULTI` decimal(6,5) NOT NULL DEFAULT '0.00000',
  `TAX_DIVIDE` decimal(6,5) NOT NULL DEFAULT '0.00000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `payment`
--

CREATE TABLE `payment` (
  `PAYMENTID` bigint(21) UNSIGNED NOT NULL,
  `MYID` bigint(21) UNSIGNED NOT NULL,
  `INVOICEID` bigint(21) UNSIGNED NOT NULL,
  `PAYMENT_DATE` date NOT NULL DEFAULT '0000-00-00',
  `METHODOFPAYID` tinyint(3) UNSIGNED NOT NULL,
  `METHOD_OF_PAY` varchar(150) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `CARDNR` blob NOT NULL,
  `VALIDTHRU` blob NOT NULL,
  `SUM_PAID` decimal(21,2) NOT NULL DEFAULT '0.00',
  `NOTE` text COLLATE latin1_german2_ci NOT NULL,
  `CANCELED` tinyint(3) UNSIGNED NOT NULL,
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `posgroup`
--

CREATE TABLE `posgroup` (
  `POSGROUPID` smallint(5) UNSIGNED NOT NULL,
  `DESCRIPTION` varchar(150) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `setting`
--

CREATE TABLE `setting` (
  `SETTINGID` smallint(5) UNSIGNED NOT NULL,
  `COMPANY_DATE` date NOT NULL DEFAULT '0000-00-00',
  `PRINT_COMPANY_DATA` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `PRINT_POSITION_NAME` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `TAX_FREE` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `COMPANY_NAME` varchar(150) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_ADDRESS` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_POSTAL` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_CITY` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_COUNTRY` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_PHONE` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_FAX` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_EMAIL` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_URL` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_WAP` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_CURRENCY` varchar(10) COLLATE latin1_german2_ci NOT NULL DEFAULT 'EUR',
  `COMPANY_SALESPRICE` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `COMPANY_TAXNR` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_BUSINESS_TAXNR` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_BANKNAME` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_BANKACCOUNT` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_BANKNUMBER` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_BANKIBAN` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_BANKBIC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `EMAIL_INTERNAL` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `EMAIL_USE_SIGNATURE` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `EMAIL_SIGNATURE` text COLLATE latin1_german2_ci NOT NULL,
  `INVENTORY_CHECK_ACTIVE` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `REMINDER` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `REMINDER_DAYS` tinyint(3) UNSIGNED NOT NULL DEFAULT '10',
  `REMINDER_PRICE` decimal(11,2) NOT NULL DEFAULT '2.50',
  `ENTRYS_PER_PAGE` smallint(5) UNSIGNED NOT NULL DEFAULT '50',
  `SESSION_SEC` smallint(5) UNSIGNED NOT NULL DEFAULT '600',
  `COMPANY_LOGO` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_LOGO_WIDTH` varchar(10) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `COMPANY_LOGO_HEIGHT` varchar(10) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `PDF_COMPANY_LOGO_HEIGHT` tinyint(3) UNSIGNED NOT NULL DEFAULT '15',
  `PDF_COMPANY_LOGO_WIDTH` tinyint(3) UNSIGNED NOT NULL DEFAULT '50',
  `PDF_FONT` varchar(30) COLLATE latin1_german2_ci NOT NULL DEFAULT 'Arial',
  `PDF_DIR` varchar(254) COLLATE latin1_german2_ci NOT NULL DEFAULT '/tmp/',
  `PDF_FONT_SIZE1` tinyint(3) UNSIGNED NOT NULL DEFAULT '9',
  `PDF_FONT_SIZE2` tinyint(3) UNSIGNED NOT NULL DEFAULT '10',
  `PDF_TYPE_HEIGHT` tinyint(3) UNSIGNED NOT NULL DEFAULT '22',
  `PDF_ATTACHMENT_TEXT` text COLLATE latin1_german2_ci NOT NULL,
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `syslog`
--

CREATE TABLE `syslog` (
  `SYSLOGID` bigint(21) UNSIGNED NOT NULL,
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DESCRIPTION` text COLLATE latin1_german2_ci NOT NULL,
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tax`
--

CREATE TABLE `tax` (
  `TAXID` tinyint(3) UNSIGNED NOT NULL,
  `TAX_DIVIDE` decimal(6,5) NOT NULL DEFAULT '0.00000',
  `TAX_MULTI` decimal(6,5) NOT NULL DEFAULT '0.00000',
  `TAX_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL,
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `USERGROUP1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `USERGROUP2` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tmp_invoice`
--

CREATE TABLE `tmp_invoice` (
  `TMP_INVOICEID` bigint(21) UNSIGNED NOT NULL,
  `MYID` bigint(21) UNSIGNED NOT NULL,
  `INVOICEID` bigint(21) UNSIGNED NOT NULL,
  `POSITIONID` bigint(21) UNSIGNED NOT NULL,
  `USERNAME` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `POS_DESC` text COLLATE latin1_german2_ci NOT NULL,
  `POS_QUANTITY` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_PRICE` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_GROUP` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX` tinyint(3) UNSIGNED NOT NULL,
  `TAX_MULTI` decimal(6,5) NOT NULL DEFAULT '0.00000',
  `TAX_DIVIDE` decimal(6,5) NOT NULL DEFAULT '0.00000',
  `TAX_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tmp_offer`
--

CREATE TABLE `tmp_offer` (
  `TMP_OFFERID` bigint(21) UNSIGNED NOT NULL,
  `MYID` bigint(21) UNSIGNED NOT NULL,
  `OFFERID` bigint(21) UNSIGNED NOT NULL,
  `POSITIONID` bigint(21) UNSIGNED NOT NULL,
  `USERNAME` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `POS_DESC` text COLLATE latin1_german2_ci NOT NULL,
  `POS_QUANTITY` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_PRICE` decimal(21,2) NOT NULL DEFAULT '0.00',
  `POS_GROUP` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `TAX` tinyint(3) UNSIGNED NOT NULL,
  `TAX_MULTI` decimal(6,5) NOT NULL DEFAULT '0.00000',
  `TAX_DIVIDE` decimal(6,5) NOT NULL DEFAULT '0.00000',
  `TAX_DESC` varchar(50) COLLATE latin1_german2_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `updatetable`
--

CREATE TABLE `updatetable` (
  `UPDATEID` bigint(21) UNSIGNED NOT NULL,
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `VERSION` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `LOGINUPDATE` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `TABLEUPDATE` tinyint(3) UNSIGNED NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `USERID` int(11) UNSIGNED NOT NULL,
  `FULLNAME` blob NOT NULL,
  `USERNAME` blob NOT NULL,
  `PASSWORD` blob NOT NULL,
  `USERGROUP1` blob NOT NULL,
  `USERGROUP2` blob NOT NULL,
  `LANGUAGE` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `USER_ACTIVE` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `LICENSE_ACCEPTED` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `CREATEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `MODIFIEDBY` varchar(100) COLLATE latin1_german2_ci NOT NULL DEFAULT 'admin',
  `CREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci ROW_FORMAT=DYNAMIC;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `addressbook`
--
ALTER TABLE `addressbook`
  ADD PRIMARY KEY (`MYID`),
  ADD KEY `LASTNAME` (`LASTNAME`(20)),
  ADD KEY `FIRSTNAME` (`FIRSTNAME`(20)),
  ADD KEY `COMPANY` (`COMPANY`(20));

--
-- Indizes für die Tabelle `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`POSITIONID`),
  ADD KEY `POS_NAME` (`POS_NAME`(20)),
  ADD KEY `POS_DESC` (`POS_DESC`(20));

--
-- Indizes für die Tabelle `cashbook`
--
ALTER TABLE `cashbook`
  ADD PRIMARY KEY (`CASHBOOKID`),
  ADD KEY `MYID` (`MYID`),
  ADD KEY `INVOICEID` (`INVOICEID`),
  ADD KEY `PAYMENTID` (`PAYMENTID`),
  ADD KEY `DESCRIPTION` (`DESCRIPTION`(20));

--
-- Indizes für die Tabelle `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`CATEGORYID`),
  ADD KEY `DESCRIPTION` (`DESCRIPTION`(20));

--
-- Indizes für die Tabelle `customerpos`
--
ALTER TABLE `customerpos`
  ADD PRIMARY KEY (`CUSTOMERPOSID`),
  ADD KEY `MYID` (`MYID`),
  ADD KEY `POSITIONID` (`POSITIONID`);

--
-- Indizes für die Tabelle `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`INVOICEID`),
  ADD KEY `MYID` (`MYID`);

--
-- Indizes für die Tabelle `invoicepos`
--
ALTER TABLE `invoicepos`
  ADD PRIMARY KEY (`INVOICEPOSID`),
  ADD KEY `MYID` (`MYID`),
  ADD KEY `INVOICEID` (`INVOICEID`),
  ADD KEY `POSITIONID` (`POSITIONID`);

--
-- Indizes für die Tabelle `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`MESSAGEID`),
  ADD KEY `DESCRIPTION` (`DESCRIPTION`(20));

--
-- Indizes für die Tabelle `methodofpay`
--
ALTER TABLE `methodofpay`
  ADD PRIMARY KEY (`METHODOFPAYID`),
  ADD KEY `DESCRIPTION` (`DESCRIPTION`(20));

--
-- Indizes für die Tabelle `offer`
--
ALTER TABLE `offer`
  ADD PRIMARY KEY (`OFFERID`),
  ADD KEY `MYID` (`MYID`),
  ADD KEY `INVOICEID` (`INVOICEID`);

--
-- Indizes für die Tabelle `offerpos`
--
ALTER TABLE `offerpos`
  ADD PRIMARY KEY (`OFFERPOSID`),
  ADD KEY `OFFERID` (`OFFERID`),
  ADD KEY `MYID` (`MYID`),
  ADD KEY `POSITIONID` (`POSITIONID`);

--
-- Indizes für die Tabelle `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PAYMENTID`),
  ADD KEY `MYID` (`MYID`),
  ADD KEY `INVOICEID` (`INVOICEID`);

--
-- Indizes für die Tabelle `posgroup`
--
ALTER TABLE `posgroup`
  ADD PRIMARY KEY (`POSGROUPID`),
  ADD KEY `DESCRIPTION` (`DESCRIPTION`(20));

--
-- Indizes für die Tabelle `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`SETTINGID`),
  ADD UNIQUE KEY `COMPANY_NAME` (`COMPANY_NAME`);

--
-- Indizes für die Tabelle `syslog`
--
ALTER TABLE `syslog`
  ADD PRIMARY KEY (`SYSLOGID`),
  ADD KEY `DESCRIPTION` (`DESCRIPTION`(20));

--
-- Indizes für die Tabelle `tax`
--
ALTER TABLE `tax`
  ADD PRIMARY KEY (`TAXID`);

--
-- Indizes für die Tabelle `tmp_invoice`
--
ALTER TABLE `tmp_invoice`
  ADD PRIMARY KEY (`TMP_INVOICEID`),
  ADD KEY `MYID` (`MYID`),
  ADD KEY `INVOICEID` (`INVOICEID`),
  ADD KEY `POSITIONID` (`POSITIONID`);

--
-- Indizes für die Tabelle `tmp_offer`
--
ALTER TABLE `tmp_offer`
  ADD PRIMARY KEY (`TMP_OFFERID`),
  ADD KEY `MYID` (`MYID`),
  ADD KEY `OFFERID` (`OFFERID`),
  ADD KEY `POSITIONID` (`POSITIONID`);

--
-- Indizes für die Tabelle `updatetable`
--
ALTER TABLE `updatetable`
  ADD PRIMARY KEY (`UPDATEID`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`USERID`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`(30));

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `addressbook`
--
ALTER TABLE `addressbook`
  MODIFY `MYID` bigint(21) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `article`
--
ALTER TABLE `article`
  MODIFY `POSITIONID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `cashbook`
--
ALTER TABLE `cashbook`
  MODIFY `CASHBOOKID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `category`
--
ALTER TABLE `category`
  MODIFY `CATEGORYID` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `customerpos`
--
ALTER TABLE `customerpos`
  MODIFY `CUSTOMERPOSID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invoice`
--
ALTER TABLE `invoice`
  MODIFY `INVOICEID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invoicepos`
--
ALTER TABLE `invoicepos`
  MODIFY `INVOICEPOSID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `message`
--
ALTER TABLE `message`
  MODIFY `MESSAGEID` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `methodofpay`
--
ALTER TABLE `methodofpay`
  MODIFY `METHODOFPAYID` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `offer`
--
ALTER TABLE `offer`
  MODIFY `OFFERID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `offerpos`
--
ALTER TABLE `offerpos`
  MODIFY `OFFERPOSID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `payment`
--
ALTER TABLE `payment`
  MODIFY `PAYMENTID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `posgroup`
--
ALTER TABLE `posgroup`
  MODIFY `POSGROUPID` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `setting`
--
ALTER TABLE `setting`
  MODIFY `SETTINGID` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `syslog`
--
ALTER TABLE `syslog`
  MODIFY `SYSLOGID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tax`
--
ALTER TABLE `tax`
  MODIFY `TAXID` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tmp_invoice`
--
ALTER TABLE `tmp_invoice`
  MODIFY `TMP_INVOICEID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tmp_offer`
--
ALTER TABLE `tmp_offer`
  MODIFY `TMP_OFFERID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `updatetable`
--
ALTER TABLE `updatetable`
  MODIFY `UPDATEID` bigint(21) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `USERID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

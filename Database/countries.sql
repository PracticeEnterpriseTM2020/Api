-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2020 at 09:20 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enerjoy`
--

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `name` varchar(100) NOT NULL,
  `abv` char(2) NOT NULL DEFAULT '' COMMENT 'ISO 3661-1 alpha-2',
  `id` bigint(20) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`name`, `abv`, `id`) VALUES
('Afghanistan', 'AF', 1),
('Aland Islands', 'AX', 2),
('Albania', 'AL', 3),
('Algeria', 'DZ', 4),
('American Samoa', 'AS', 5),
('Andorra', 'AD', 6),
('Angola', 'AO', 7),
('Anguilla', 'AI', 8),
('Antigua and Barbuda', 'AG', 9),
('Argentina', 'AR', 10),
('Armenia', 'AM', 11),
('Aruba', 'AW', 12),
('Australia', 'AU', 13),
('Austria', 'AT', 14),
('Azerbaijan', 'AZ', 15),
('Bahamas', 'BS', 16),
('Bahrain', 'BH', 17),
('Bangladesh', 'BD', 18),
('Barbados', 'BB', 19),
('Belarus', 'BY', 20),
('Belgium', 'BE', 21),
('Belize', 'BZ', 22),
('Benin', 'BJ', 23),
('Bermuda', 'BM', 24),
('Bhutan', 'BT', 25),
('Bolivia', 'BO', 26),
('Bosnia and Herzegovina', 'BA', 27),
('Botswana', 'BW', 28),
('Brazil', 'BR', 29),
('British Virgin Islands', 'VG', 30),
('Brunei Darussalam', 'BN', 31),
('Bulgaria', 'BG', 32),
('Burkina Faso', 'BF', 33),
('Burundi', 'BI', 34),
('Cambodia', 'KH', 35),
('Cameroon', 'CM', 36),
('Canada', 'CA', 37),
('Cape Verde', 'CV', 38),
('Cayman Islands', 'KY', 39),
('Central African Republic', 'CF', 40),
('Chad', 'TD', 41),
('Chile', 'CL', 42),
('China', 'CN', 43),
('Colombia', 'CO', 44),
('Comoros', 'KM', 45),
('Congo', 'CG', 46),
('Cook Islands', 'CK', 47),
('Costa Rica', 'CR', 48),
('Cote d\'Ivoire', 'CI', 49),
('Croatia', 'HR', 50),
('Cuba', 'CU', 51),
('Cyprus', 'CY', 52),
('Czech Republic', 'CZ', 53),
('Democratic Republic of the Congo', 'CD', 54),
('Denmark', 'DK', 55),
('Djibouti', 'DJ', 56),
('Dominica', 'DM', 57),
('Dominican Republic', 'DO', 58),
('Ecuador', 'EC', 59),
('Egypt', 'EG', 60),
('El Salvador', 'SV', 61),
('Equatorial Guinea', 'GQ', 62),
('Eritrea', 'ER', 63),
('Estonia', 'EE', 64),
('Ethiopia', 'ET', 65),
('Faeroe Islands', 'FO', 66),
('Falkland Islands', 'FK', 67),
('Fiji', 'FJ', 68),
('Finland', 'FI', 69),
('France', 'FR', 70),
('French Guiana', 'GF', 71),
('French Polynesia', 'PF', 72),
('Gabon', 'GA', 73),
('Gambia', 'GM', 74),
('Georgia', 'GE', 75),
('Germany', 'DE', 76),
('Ghana', 'GH', 77),
('Gibraltar', 'GI', 78),
('Greece', 'GR', 79),
('Greenland', 'GL', 80),
('Grenada', 'GD', 81),
('Guadeloupe', 'GP', 82),
('Guam', 'GU', 83),
('Guatemala', 'GT', 84),
('Guernsey', 'GG', 85),
('Guinea', 'GN', 86),
('Guinea-Bissau', 'GW', 87),
('Guyana', 'GY', 88),
('Haiti', 'HT', 89),
('Holy See', 'VA', 90),
('Honduras', 'HN', 91),
('Hong Kong', 'HK', 92),
('Hungary', 'HU', 93),
('Iceland', 'IS', 94),
('India', 'IN', 95),
('Indonesia', 'ID', 96),
('Iran', 'IR', 97),
('Iraq', 'IQ', 98),
('Ireland', 'IE', 99),
('Isle of Man', 'IM', 100),
('Israel', 'IL', 101),
('Italy', 'IT', 102),
('Jamaica', 'JM', 103),
('Japan', 'JP', 104),
('Jersey', 'JE', 105),
('Jordan', 'JO', 106),
('Kazakhstan', 'KZ', 107),
('Kenya', 'KE', 108),
('Kiribati', 'KI', 109),
('Kuwait', 'KW', 110),
('Kyrgyzstan', 'KG', 111),
('Laos', 'LA', 112),
('Latvia', 'LV', 113),
('Lebanon', 'LB', 114),
('Lesotho', 'LS', 115),
('Liberia', 'LR', 116),
('Libyan Arab Jamahiriya', 'LY', 117),
('Liechtenstein', 'LI', 118),
('Lithuania', 'LT', 119),
('Luxembourg', 'LU', 120),
('Macao', 'MO', 121),
('Macedonia', 'MK', 122),
('Madagascar', 'MG', 123),
('Malawi', 'MW', 124),
('Malaysia', 'MY', 125),
('Maldives', 'MV', 126),
('Mali', 'ML', 127),
('Malta', 'MT', 128),
('Marshall Islands', 'MH', 129),
('Martinique', 'MQ', 130),
('Mauritania', 'MR', 131),
('Mauritius', 'MU', 132),
('Mayotte', 'YT', 133),
('Mexico', 'MX', 134),
('Micronesia', 'FM', 135),
('Moldova', 'MD', 136),
('Monaco', 'MC', 137),
('Mongolia', 'MN', 138),
('Montenegro', 'ME', 139),
('Montserrat', 'MS', 140),
('Morocco', 'MA', 141),
('Mozambique', 'MZ', 142),
('Myanmar', 'MM', 143),
('Namibia', 'NA', 144),
('Nauru', 'NR', 145),
('Nepal', 'NP', 146),
('Netherlands', 'NL', 147),
('Netherlands Antilles', 'AN', 148),
('New Caledonia', 'NC', 149),
('New Zealand', 'NZ', 150),
('Nicaragua', 'NI', 151),
('Niger', 'NE', 152),
('Nigeria', 'NG', 153),
('Niue', 'NU', 154),
('Norfolk Island', 'NF', 155),
('North Korea', 'KP', 156),
('Northern Mariana Islands', 'MP', 157),
('Norway', 'NO', 158),
('Oman', 'OM', 159),
('Pakistan', 'PK', 160),
('Palau', 'PW', 161),
('Palestine', 'PS', 162),
('Panama', 'PA', 163),
('Papua New Guinea', 'PG', 164),
('Paraguay', 'PY', 165),
('Peru', 'PE', 166),
('Philippines', 'PH', 167),
('Pitcairn', 'PN', 168),
('Poland', 'PL', 169),
('Portugal', 'PT', 170),
('Puerto Rico', 'PR', 171),
('Qatar', 'QA', 172),
('Reunion', 'RE', 173),
('Romania', 'RO', 174),
('Russian Federation', 'RU', 175),
('Rwanda', 'RW', 176),
('Saint Helena', 'SH', 177),
('Saint Kitts and Nevis', 'KN', 178),
('Saint Lucia', 'LC', 179),
('Saint Pierre and Miquelon', 'PM', 180),
('Saint Vincent and the Grenadines', 'VC', 181),
('Saint-Barthelemy', 'BL', 182),
('Saint-Martin', 'MF', 183),
('Samoa', 'WS', 184),
('San Marino', 'SM', 185),
('Sao Tome and Principe', 'ST', 186),
('Saudi Arabia', 'SA', 187),
('Senegal', 'SN', 188),
('Serbia', 'RS', 189),
('Seychelles', 'SC', 190),
('Sierra Leone', 'SL', 191),
('Singapore', 'SG', 192),
('Slovakia', 'SK', 193),
('Slovenia', 'SI', 194),
('Solomon Islands', 'SB', 195),
('Somalia', 'SO', 196),
('South Africa', 'ZA', 197),
('South Korea', 'KR', 198),
('South Sudan', 'SS', 199),
('Spain', 'ES', 200),
('Sri Lanka', 'LK', 201),
('Sudan', 'SD', 202),
('Suriname', 'SR', 203),
('Svalbard and Jan Mayen Islands', 'SJ', 204),
('Swaziland', 'SZ', 205),
('Sweden', 'SE', 206),
('Switzerland', 'CH', 207),
('Syrian Arab Republic', 'SY', 208),
('Tajikistan', 'TJ', 209),
('Tanzania', 'TZ', 210),
('Thailand', 'TH', 211),
('Timor-Leste', 'TP', 212),
('Togo', 'TG', 213),
('Tokelau', 'TK', 214),
('Tonga', 'TO', 215),
('Trinidad and Tobago', 'TT', 216),
('Tunisia', 'TN', 217),
('Turkey', 'TR', 218),
('Turkmenistan', 'TM', 219),
('Turks and Caicos Islands', 'TC', 220),
('Tuvalu', 'TV', 221),
('U.S. Virgin Islands', 'VI', 222),
('Uganda', 'UG', 223),
('Ukraine', 'UA', 224),
('United Arab Emirates', 'AE', 225),
('United Kingdom', 'UK', 226),
('United States', 'US', 227),
('Uruguay', 'UY', 228),
('Uzbekistan', 'UZ', 229),
('Vanuatu', 'VU', 230),
('Venezuela', 'VE', 231),
('Viet Nam', 'VN', 232),
('Wallis and Futuna Islands', 'WF', 233),
('Western Sahara', 'EH', 234),
('Yemen', 'YE', 235),
('Zambia', 'ZM', 236),
('Zimbabwe', 'ZW', 237);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=238;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

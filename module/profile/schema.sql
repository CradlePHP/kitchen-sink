--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `profile_id` int(10) UNSIGNED NOT NULL COMMENT 'Database Generated',
  `profile_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `profile_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `profile_phone` varchar(255) DEFAULT NULL,
  `profile_slug` varchar(255) DEFAULT NULL,
  `profile_detail` text CHARACTER SET utf8 COLLATE utf8_bin,
  `profile_image` varchar(255) DEFAULT NULL,
  `profile_company` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `profile_job` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `profile_gender` varchar(255) DEFAULT NULL,
  `profile_birth` datetime DEFAULT NULL,
  `profile_website` varchar(255) DEFAULT NULL,
  `profile_facebook` varchar(255) DEFAULT NULL,
  `profile_linkedin` varchar(255) DEFAULT NULL,
  `profile_twitter` varchar(255) DEFAULT NULL,
  `profile_google` varchar(255) DEFAULT NULL,
  `profile_active` int(1) UNSIGNED NOT NULL DEFAULT '1',
  `profile_rank` varchar(255) DEFAULT NULL,
  `profile_type` varchar(255) DEFAULT NULL,
  `profile_flag` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `profile_created` datetime NOT NULL,
  `profile_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `profile_active` (`profile_active`),
  ADD KEY `profile_type` (`profile_type`),
  ADD KEY `profile_flag` (`profile_flag`),
  ADD KEY `profile_created` (`profile_created`),
  ADD KEY `profile_updated` (`profile_updated`);

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
MODIFY `profile_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Database Generated';

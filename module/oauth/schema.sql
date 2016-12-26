--
-- Table structure for table `app`
--

CREATE TABLE `app` (
  `app_id` int(10) UNSIGNED NOT NULL COMMENT 'Database Generated',
  `app_name` varchar(255) NOT NULL,
  `app_domain` varchar(255) DEFAULT NULL,
  `app_website` varchar(255) DEFAULT NULL,
  `app_permissions` json NOT NULL,
  `app_token` varchar(255) DEFAULT NULL,
  `app_secret` varchar(255) DEFAULT NULL,
  `app_active` int(1) UNSIGNED NOT NULL DEFAULT '1',
  `app_type` varchar(255) DEFAULT NULL,
  `app_flag` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `app_created` datetime NOT NULL,
  `app_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_profile`
--

CREATE TABLE `app_profile` (
  `app_id` int(10) UNSIGNED NOT NULL,
  `profile_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `auth`
--

CREATE TABLE `auth` (
  `auth_id` int(10) UNSIGNED NOT NULL COMMENT 'Database Generated',
  `auth_slug` varchar(255) NOT NULL COMMENT 'can be an email or slug',
  `auth_password` varchar(255) NOT NULL COMMENT 'md5 hash',
  `auth_token` varchar(255) NOT NULL COMMENT 'System Generated',
  `auth_secret` varchar(255) NOT NULL COMMENT 'System Generated',
  `auth_permissions` json NOT NULL COMMENT 'See permissions.json for options',
  `auth_facebook_token` varchar(255) DEFAULT NULL COMMENT 'Facebook access token',
  `auth_facebook_secret` varchar(255) DEFAULT NULL COMMENT 'Facebook access secret',
  `auth_linkedin_token` varchar(255) DEFAULT NULL COMMENT 'LinkedIn access token',
  `auth_linkedin_secret` varchar(255) DEFAULT NULL COMMENT 'LinkedIn access secret',
  `auth_twitter_token` varchar(255) DEFAULT NULL COMMENT 'Twitter access token',
  `auth_twitter_secret` varchar(255) DEFAULT NULL COMMENT 'Twitter access secret',
  `auth_google_token` varchar(255) DEFAULT NULL COMMENT 'Google access token',
  `auth_google_secret` varchar(255) DEFAULT NULL COMMENT 'Google access secret',
  `auth_active` int(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Do not delete rows',
  `auth_type` varchar(255) DEFAULT NULL COMMENT 'General usage type',
  `auth_flag` int(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'General usage flag',
  `auth_created` datetime NOT NULL COMMENT 'System Generated',
  `auth_updated` datetime NOT NULL COMMENT 'System Generated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `auth_profile`
--

CREATE TABLE `auth_profile` (
  `auth_id` int(10) UNSIGNED NOT NULL,
  `profile_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` int(10) UNSIGNED NOT NULL COMMENT 'Database Generated',
  `session_token` varchar(255) NOT NULL COMMENT 'System Generated',
  `session_secret` varchar(255) NOT NULL COMMENT 'System Generated',
  `session_permissions` json NOT NULL COMMENT 'See permissions.json for options',
  `session_status` varchar(255) NOT NULL DEFAULT 'PENDING' COMMENT 'eg. PENDING, ACCESS etc.',
  `session_active` int(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Do not delete rows',
  `session_type` varchar(255) DEFAULT NULL COMMENT 'General usage type',
  `session_flag` int(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'General usage flag',
  `session_created` datetime NOT NULL COMMENT 'System Generated',
  `session_updated` datetime NOT NULL COMMENT 'System Generated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_app`
--

CREATE TABLE `session_app` (
  `session_id` int(10) UNSIGNED NOT NULL,
  `app_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_auth`
--

CREATE TABLE `session_auth` (
  `session_id` int(10) UNSIGNED NOT NULL,
  `auth_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app`
--
ALTER TABLE `app`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `app_active` (`app_active`),
  ADD KEY `app_type` (`app_type`),
  ADD KEY `app_flag` (`app_flag`),
  ADD KEY `app_created` (`app_created`),
  ADD KEY `app_updated` (`app_updated`);

--
-- Indexes for table `app_profile`
--
ALTER TABLE `app_profile`
  ADD PRIMARY KEY (`app_id`,`profile_id`),
  ADD KEY `profile_id_idx` (`profile_id`);

--
-- Indexes for table `auth`
--
ALTER TABLE `auth`
  ADD PRIMARY KEY (`auth_id`),
  ADD UNIQUE KEY `auth_slug` (`auth_slug`),
  ADD KEY `auth_password` (`auth_password`),
  ADD KEY `auth_token` (`auth_token`),
  ADD KEY `auth_secret` (`auth_secret`),
  ADD KEY `auth_active` (`auth_active`),
  ADD KEY `auth_type` (`auth_type`),
  ADD KEY `auth_flag` (`auth_flag`),
  ADD KEY `auth_created` (`auth_created`),
  ADD KEY `auth_updated` (`auth_updated`);

--
-- Indexes for table `auth_profile`
--
ALTER TABLE `auth_profile`
  ADD PRIMARY KEY (`auth_id`,`profile_id`),
  ADD KEY `profile_id_idx` (`profile_id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `session_token` (`session_token`),
  ADD KEY `session_secret` (`session_secret`),
  ADD KEY `session_status` (`session_status`),
  ADD KEY `session_active` (`session_active`),
  ADD KEY `session_type` (`session_type`),
  ADD KEY `session_flag` (`session_flag`),
  ADD KEY `session_created` (`session_created`),
  ADD KEY `session_updated` (`session_updated`);

--
-- Indexes for table `session_app`
--
ALTER TABLE `session_app`
  ADD PRIMARY KEY (`session_id`,`app_id`),
  ADD KEY `app_id_idx` (`app_id`);

--
-- Indexes for table `session_auth`
--
ALTER TABLE `session_auth`
  ADD PRIMARY KEY (`session_id`,`auth_id`),
  ADD KEY `auth_id_idx` (`auth_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app`
--
ALTER TABLE `app`
  MODIFY `app_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Database Generated';
--
-- AUTO_INCREMENT for table `auth`
--
ALTER TABLE `auth`
  MODIFY `auth_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Database Generated';
--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `session_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Database Generated';

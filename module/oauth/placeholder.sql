--
-- Dumping data for table `app`
--

INSERT INTO `app` (`app_id`, `app_name`, `app_domain`, `app_website`, `app_permissions`, `app_token`, `app_secret`, `app_active`, `app_type`, `app_flag`, `app_created`, `app_updated`) VALUES
(1, 'Cradle App 1', '*.cradlephp.github.io', 'http://cradlephp.github.io', '["public_profile", "personal_profile"]', '87d02468a934cb717cc15fe48a244f43', '21e21453cad34a94b76fb840c1eeba8a', 1, 'admin', 0, '2016-12-21 07:37:43', '2016-12-21 08:06:03');

--
-- Dumping data for table `app_profile`
--

INSERT INTO `app_profile` (`app_id`, `profile_id`) VALUES
(1, 1);

--
-- Dumping data for table `auth`
--

INSERT INTO `auth` (`auth_id`, `auth_slug`, `auth_password`, `auth_token`, `auth_secret`, `auth_permissions`, `auth_facebook_token`, `auth_facebook_secret`, `auth_linkedin_token`, `auth_linkedin_secret`, `auth_twitter_token`, `auth_twitter_secret`, `auth_google_token`, `auth_google_secret`, `auth_active`, `auth_type`, `auth_flag`, `auth_created`, `auth_updated`) VALUES
(1, 'john@doe.com', '202cb962ac59075b964b07152d234b70', '8323fd20795498fb77deb36a85fd3490', '300248246ea1996063a1a40635dbce71', '["public_profile", "personal_profile"]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'admin', 0, '2016-12-21 07:36:51', '2016-12-21 08:08:45');

--
-- Dumping data for table `auth_profile`
--

INSERT INTO `auth_profile` (`auth_id`, `profile_id`) VALUES
(1, 1);

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`session_id`, `session_token`, `session_secret`, `session_permissions`, `session_status`, `session_active`, `session_type`, `session_flag`, `session_created`, `session_updated`) VALUES
(1, 'eea8e9cb6302e2e83e38737bad5ed194', 'd815589580f5213d3f915eb6d13724e4', '["public_profile", "personal_profile"]', 'PENDING', 1, NULL, 0, '2016-12-21 07:58:18', '2016-12-21 07:58:18');

--
-- Dumping data for table `session_app`
--

INSERT INTO `session_app` (`session_id`, `app_id`) VALUES
(1, 1);

--
-- Dumping data for table `session_auth`
--

INSERT INTO `session_auth` (`session_id`, `auth_id`) VALUES
(1, 1);

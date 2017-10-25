Proxy rotator 
=================

Keeps a list of proxies alive and updated. 
Uses Eloquent to manege a simple database table withe the proxies. 
Scrapes some sites as a proxy source.

# Needs database table to store proxy information 
```mysql
--
-- Table structure for table `proxy`
--

CREATE TABLE `proxy` (
  `id` int(11) NOT NULL,
  `ip` varchar(256) NOT NULL,
  `port` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
--
-- Indexes for table `proxy`
--
ALTER TABLE `proxy`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `proxy`
--
ALTER TABLE `proxy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
```
LOAD DATA LOCAL INFILE '........./schoology/analytics/raw/aliased.csv' into table data FIELDS TERMINATED BY ',' ENCLOSED BY '\"';


#   Edit the my.cnf configuration file /etc/mysql/my.cnf
#
#       [mysqld]
#       secure_file_priv=""
#       local-infile
#       
#       [mysql]
#       local-infile
#       
#       [client]
#       loose-local-infile = 1
#       
#
#   Then restart mysql
#
#       sudo systemctl restart mysql




#   The following will loosen consistency checks, but speed up the loading
#
#     SET UNIQUE_CHECKS = 0;
#     SET FOREIGN_KEY_CHECKS = 0;


#   Then re-enable them when finished
#
#     SET UNIQUE_CHECKS = 1;
#     SET FOREIGN_KEY_CHECKS = 1;


#   You can also simply run the provided sample1.php code to load the data,
#   however, it will be much slower: about 10 minutes vs. 2 seconds using LOAD DATA LOCAL INFILE.
#   Using PHP also requires a lot of memory, and you might have to increase the memory limit.

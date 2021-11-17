#  Usage Analytics Queries for Schoology (exported data)
#  Use as guidlines for generating views.

# Show all Views in the database
SHOW FULL TABLES IN schoology WHERE TABLE_TYPE LIKE "VIEW";

#------------------------------------------------------------------------------------------------------------#
# Number of Students that have viewed the Schoology Orientation course
# (# unique students that viewed orientation course)/(# of unique students that viewed all DVHS courses)

    #%%%%%%%%%%%%%%%%%%%  Google Charts Queries  %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    # DoDEA Virtual School: Schoology Orientation Usage
    SELECT setA.roleName AS Role, CASE WHEN CountA IS NULL THEN 0 ELSE CASE WHEN CountB > 0 THEN 100.0*CountA/CountB ELSE 0 END END AS '% Users that viewed "Schoology Orientation" Course' FROM (SELECT COUNT(DISTINCT(schoologyUserId)) AS CountB, roleName, courseName, itemBuildingName FROM data GROUP BY itemBuildingName, roleName HAVING itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development') ORDER BY courseName = 'Schoology Orientation') AS setA INNER JOIN (SELECT COUNT(DISTINCT(schoologyUserId)) AS CountA, courseId, courseName, roleName, itemBuildingId, itemBuildingName FROM data GROUP BY itemBuildingId, roleName, courseName HAVING courseName LIKE 'Schoology Orientation' AND itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development')) AS setB ON (setA.roleName = setB.roleName AND setA.itemBuildingName = setB.itemBuildingName AND setA.itemBuildingName = 'DoDEA Virtual School') ORDER BY setA.itemBuildingName DESC;

    # DoDEA - Professional Development: Schoology Orientation Usage
    SELECT setA.roleName AS Role, CASE WHEN CountA IS NULL THEN 0 ELSE CASE WHEN CountB > 0 THEN 100.0*CountA/CountB ELSE 0 END END AS '% Users that viewed "Schoology Orientation" Course' FROM (SELECT COUNT(DISTINCT(schoologyUserId)) AS CountB, roleName, courseName, itemBuildingName FROM data GROUP BY itemBuildingName, roleName HAVING itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development') ORDER BY courseName = 'Schoology Orientation') AS setA LEFT JOIN (SELECT COUNT(DISTINCT(schoologyUserId)) AS CountA, courseId, courseName, roleName, itemBuildingId, itemBuildingName FROM data GROUP BY itemBuildingId, roleName, courseName HAVING courseName LIKE 'Schoology Orientation' AND itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development')) AS setB ON (setA.roleName = setB.roleName AND setA.itemBuildingName = setB.itemBuildingName AND setA.itemBuildingName = 'DoDEA - Professional Development') ORDER BY setA.itemBuildingName DESC;

    #%%%%%%%%%%%%%%%%%%%  Detailed Query  %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    # NUmber of unique students/users who have viewed the Schoology Orientation Course group by Building and role VS all users in DVHS grouped by role
    # Is it correct to assume that all teachers, students, Technologists, Staff in DVHS should have viewed the orientation at least once?
    # Details
    SELECT CASE WHEN CountA IS NULL THEN 'N/A' ELSE CASE WHEN CountB > 0 THEN 100.0*CountA/CountB ELSE 'N/A' END END AS 'Percent', CASE WHEN CountA IS NULL THEN 0 ELSE CountA END as 'Users that Read Orientation', CountB AS 'Total Users', setA.roleName, setB.roleName, setA.itemBuildingName as Building FROM (SELECT COUNT(DISTINCT(schoologyUserId)) AS CountB, roleName, courseName, itemBuildingName FROM data GROUP BY itemBuildingName, roleName HAVING itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development') ORDER BY courseName = 'Schoology Orientation') AS setA LEFT JOIN (SELECT COUNT(DISTINCT(schoologyUserId)) AS CountA, courseId, courseName, roleName, itemBuildingId, itemBuildingName FROM data GROUP BY itemBuildingId, roleName, courseName HAVING courseName LIKE 'Schoology Orientation' AND itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development')) AS setB ON (setA.roleName = setB.roleName AND setA.itemBuildingName = setB.itemBuildingName) ORDER BY setA.itemBuildingName DESC;
    # Chart Query
    SELECT CASE WHEN CountA IS NULL THEN 0 ELSE CASE WHEN CountB > 0 THEN 100.0*CountA/CountB ELSE 0 END END AS '% Users that viewed "Schoology Orientation" Course', setA.roleName AS Role FROM (SELECT COUNT(DISTINCT(schoologyUserId)) AS CountB, roleName, courseName, itemBuildingName FROM data GROUP BY itemBuildingName, roleName HAVING itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development') ORDER BY courseName = 'Schoology Orientation') AS setA LEFT JOIN (SELECT COUNT(DISTINCT(schoologyUserId)) AS CountA, courseId, courseName, roleName, itemBuildingId, itemBuildingName FROM data GROUP BY itemBuildingId, roleName, courseName HAVING courseName LIKE 'Schoology Orientation' AND itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development')) AS setB ON (setA.roleName = setB.roleName AND setA.itemBuildingName = setB.itemBuildingName) ORDER BY setA.itemBuildingName DESC;

    #%%%%%%%%%%%%%%%%%%%  Piecemeal Queries  %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    # Number of unique students who viewed the orientation
    SELECT COUNT(DISTINCT(schoologyUserId)) AS CountA, courseId, courseName, roleName, itemBuildingId, itemBuildingName FROM data GROUP BY itemBuildingId, roleName, courseId HAVING courseName LIKE 'Schoology Orientation' AND itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development');

        # Number of unique students in DVHS (all courses)
    SELECT COUNT(DISTINCT(schoologyUserId)) AS CountB, roleName, itemBuildingName FROM data GROUP BY itemBuildingName, roleName HAVING itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development');
    SELECT COUNT(DISTINCT(schoologyUserId)) AS CountB, courseName AS garbage, roleName, itemBuildingId, itemBuildingName FROM data GROUP BY itemBuildingId, roleName HAVING itemBuildingName IN ('DoDEA Virtual School', 'DoDEA - Professional Development');

#------------------------------------------------------------------------------------------------------------#
# Actions using External Tools (LTI)
SELECT itemId, email, actionType, LEFT(itemName,40) AS itemName, roleName, itemType as Type, date, timestamp as Tstamp, LEFT(userBuildingName,20) FROM data GROUP BY userBuildingName, itemId, email HAVING itemType LIKE 'EXTERNAL_TOOL' AND roleName LIKE 'Student' limit 40;

#------------------------------------------------------------------------------------------------------------#
# User actions per-Section
SELECT actionType, roleName, email, courseCode, courseName, itemId, sectionSchoolCode, timeSpent from data group by actionType, itemId, email having sectionSchoolCode LIKE '%11372_2122_MAA4010T_YR_001YR%' AND roleName IN ('Teacher') LIMIT 40;

#------------------------------------------------------------------------------------------------------------#
# Launching videos or activities
SELECT email AS Email, roleName AS Role, LEFT(itemName,40) as Item, itemId as ItemID, courseName as Course, courseCode AS 'Course Code', LEFT(userBuildingName, 32) as Building, userBuildingId as 'Bldg ID' FROM data where itemName LIKE '%launch%' limit 6;

#------------------------------------------------------------------------------------------------------------#
# All action types
SELECT DISTINCT(actionType) FROM data LIMIT 10;

#------------------------------------------------------------------------------------------------------------#
# All roles
SELECT roleName as Role FROM data GROUP BY roleName LIMIT 20;

#------------------------------------------------------------------------------------------------------------#
# Usage by role (usage_by_role)
SELECT roleName AS Role, COUNT(roleName) AS 'Count' FROM data GROUP BY roleName LIMIT 100;

#------------------------------------------------------------------------------------------------------------#
# Students that used Schoology within Date Range, by School/Building
SELECT COUNT(email) AS 'Count', roleName AS 'Role', userBuildingName as 'Building' FROM data GROUP BY Building, Role, email HAVING Role = 'Student' LIMIT 20;

#------------------------------------------------------------------------------------------------------------#
# Student Usage by Item Type (student_usage_by_item_type)
SELECT Type, Count FROM (SELECT COUNT(itemType) AS 'Count', itemType AS 'Type', roleName AS 'Role' FROM data GROUP BY itemType HAVING itemType IN ('EXTERNAL_TOOL','PAGE','FILE','ASSIGNMENT','TEST/QUIZ','DISCUSSION', 'SCORM_PACKAGE','MEDIA_ALBUM','RESOURCE_TEST_QUIZ','RESOURCE_FOLDER') AND roleName = 'Student' LIMIT 100) AS derived;

#------------------------------------------------------------------------------------------------------------#
# Top 3 Buildings/Schools by Overall Usage (overall_usage_by_school)
SELECT userBuildingName AS 'School', COUNT(id) AS 'Count' FROM data GROUP BY userBuildingName ORDER BY COUNT(id) DESC LIMIT 3;

#------------------------------------------------------------------------------------------------------------#
# Usage by Device Type (usage_by_device_type)
SELECT COUNT(deviceType) AS 'Count', deviceType AS 'Device' FROM data GROUP BY deviceType HAVING deviceType IN ('WEB','WEB_MOBILE','ANDROID','IOS') ORDER BY Count DESC LIMIT 100;

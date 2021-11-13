#  Usage Analytics Queries for Schoology (exported data)
#  Use as guidlines for generating views.

# Show all Views in the database
SHOW FULL TABLES IN schoology WHERE TABLE_TYPE LIKE "VIEW";

# Actions using External Tools (LTI)
SELECT itemId, email, actionType, LEFT(itemName,40) AS itemName, roleName, itemType as Type, date, timestamp as Tstamp, LEFT(userBuildingName,20) FROM data GROUP BY userBuildingName, itemId, email HAVING itemType LIKE 'EXTERNAL_TOOL' AND roleName LIKE 'Student' limit 40;

# User actions per-Section
SELECT actionType, roleName, email, courseCode, courseName, itemId, sectionSchoolCode, timeSpent from data group by actionType, itemId, email having sectionSchoolCode LIKE '%11372_2122_MAA4010T_YR_001YR%' AND roleName IN ('Teacher') LIMIT 40;

# Launching videos or activities
SELECT email AS Email, roleName AS Role, LEFT(itemName,40) as Item, itemId as ItemID, courseName as Course, courseCode AS 'Course Code', LEFT(userBuildingName, 32) as Building, userBuildingId as 'Bldg ID' FROM data where itemName LIKE '%launch%' limit 6;

# All action types
SELECT DISTINCT(actionType) FROM data LIMIT 10;

# All roles
SELECT roleName as Role FROM data GROUP BY roleName LIMIT 20;

# Usage by role (usage_by_role)
SELECT roleName AS Role, COUNT(roleName) AS 'Count' FROM data GROUP BY roleName LIMIT 100;

# Students that used Schoology within Date Range, by School/Building
SELECT COUNT(email) AS 'Count', roleName AS 'Role', userBuildingName as 'Building' FROM data GROUP BY Building, Role, email HAVING Role = 'Student' LIMIT 20;

# Student Usage by Item Type (student_usage_by_item_type)
SELECT Type, Count FROM (SELECT COUNT(itemType) AS 'Count', itemType AS 'Type', roleName AS 'Role' FROM data GROUP BY itemType HAVING itemType IN ('EXTERNAL_TOOL','PAGE','FILE','ASSIGNMENT','TEST/QUIZ','DISCUSSION', 'SCORM_PACKAGE','MEDIA_ALBUM','RESOURCE_TEST_QUIZ','RESOURCE_FOLDER') AND roleName = 'Student' LIMIT 100) AS derived;

# Top 3 Buildings/Schools by Overall Usage (overall_usage_by_school)
SELECT userBuildingName AS 'School', COUNT(id) AS 'Count' FROM data GROUP BY userBuildingName ORDER BY COUNT(id) DESC LIMIT 3;

# Usage by Device Type (usage_by_device_type)
SELECT COUNT(deviceType) AS 'Count', deviceType AS 'Device' FROM data GROUP BY deviceType HAVING deviceType IN ('WEB','WEB_MOBILE','ANDROID','IOS') ORDER BY Count DESC LIMIT 100;

#  Usage Analytics Queries for Schoology (exported data)
#  Use as guidlines for generating views.

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

# Students that used Schoology within Date Range, by School/Building
SELECT COUNT(email) AS '#Users', roleName AS Role, userBuildingName as Building FROM data GROUP BY Building, Role, email HAVING Role = 'Student' LIMIT 20;
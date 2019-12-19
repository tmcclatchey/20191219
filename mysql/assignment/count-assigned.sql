SELECT 
    COUNT(`assignmentId`) as `count`
FROM
    `assignment`
WHERE
    `providerId`=:providerId
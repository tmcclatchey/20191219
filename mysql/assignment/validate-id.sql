SELECT 
    COUNT(`assignmentId`) as `count`
FROM
    `assignment`
WHERE
    `assignmentId`=:assignmentId
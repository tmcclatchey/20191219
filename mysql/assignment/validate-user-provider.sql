SELECT 
    COUNT(`assignmentId`) as `count`
FROM
    `assignment`
WHERE
    `userId`=:userId AND
    `providerId`=:providerId
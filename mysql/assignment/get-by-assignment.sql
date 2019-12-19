SELECT 
    `assignmentId`,
    `userId`,
    `providerId`,
    `roleId`
FROM
    `assignment`
WHERE
    `userId`=:userId AND
    `providerId`=:providerId
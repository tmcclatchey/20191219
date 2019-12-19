SELECT 
    `assignmentId`,
    `userId`,
    `providerId`,
    `roleId`
FROM
    `assignment`
WHERE
    `providerId`=:providerId
LIMIT
    :startingIndex, :recordLimit
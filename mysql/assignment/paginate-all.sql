SELECT 
    `assignmentId`,
    `userId`,
    `providerId`,
    `roleId`
FROM
    `assignment`
LIMIT
    :startingIndex, :recordLimit
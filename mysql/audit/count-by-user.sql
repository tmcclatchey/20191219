SELECT 
    COUNT(`auditid`) as `count`
FROM
    `audit`
WHERE
    `userId`=:userId
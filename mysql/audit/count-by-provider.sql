SELECT 
    COUNT(`auditid`) as `count`
FROM
    `audit`
WHERE
    `providerId`=:providerId
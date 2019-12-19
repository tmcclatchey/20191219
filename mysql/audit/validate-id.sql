SELECT 
    COUNT(`auditId`) as `count`
FROM
    `audit`
WHERE
    `auditId`=:auditId
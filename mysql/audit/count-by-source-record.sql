SELECT 
    COUNT(`auditid`) as `count`
FROM
    `audit`
WHERE
    `auditSource`=:auditSource AND
    `recordId`=:recordId
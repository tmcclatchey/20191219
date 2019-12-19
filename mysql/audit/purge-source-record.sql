DELETE FROM
    `audit`
WHERE
    `auditSource`=:auditSource AND
    `recordId`=:recordId
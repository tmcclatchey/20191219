SELECT
    `auditAction`,
    `auditStamp`,
    `auditSource`,
    `recordId`,
    `userId`,
    `providerId`
FROM
    `audit`
WHERE
    `auditSource`=:auditSource AND
    `recordId`=:recordId
LIMIT
    :startingIndex, :recordLimit
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
    `userId`=:userId
LIMIT
    :startingIndex, :recordLimit
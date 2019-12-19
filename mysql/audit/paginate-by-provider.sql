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
    `providerId`=:providerId
LIMIT
    :startingIndex, :recordLimit
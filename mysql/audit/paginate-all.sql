SELECT
    `auditAction`,
    `auditStamp`,
    `auditSource`,
    `recordId`,
    `userId`,
    `providerId`
FROM
    `audit`
LIMIT
    :startingIndex, :recordLimit
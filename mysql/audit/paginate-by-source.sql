SELECT
    `auditAction`,
    `auditStamp`,
    `auditSource`,
    `recordId`,
    `userId`,
    `providerId`
FROM
    `audit`
    `auditSource`=:auditSource AND
    `providerId`=:providerId
LIMIT
    :startingIndex, :recordLimit
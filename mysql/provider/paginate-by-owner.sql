SELECT
    `providerId`,
    `providerName`,
    `ownerId`
FROM
    `provider`
WHERE
    `ownerId`=:ownerId
LIMIT
    :startingIndex, :recordLimit
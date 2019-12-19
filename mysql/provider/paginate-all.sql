SELECT
    `providerId`,
    `providerName`,
    `ownerId`
FROM
    `provider`
LIMIT
    :startingIndex, :recordLimit
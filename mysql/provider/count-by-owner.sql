SELECT
    COUNT(`providerId`) AS `count`
FROM
    `provider`
WHERE
    `ownerId`=:ownerId
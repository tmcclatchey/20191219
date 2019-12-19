SELECT
    COUNT(`roleId`) AS `count`
FROM
    `role`
WHERE
    `providerId`=0 OR
    `providerId`=:providerId
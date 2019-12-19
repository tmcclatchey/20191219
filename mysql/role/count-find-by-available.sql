SELECT
    COUNT(`roleId`) AS `count`
FROM
    `role`
WHERE
    (
        `providerId`=0 OR
        `providerId`=:providerId
    )
    AND
    (
        `roleName` LIKE CONCAT('%', :query, '%') OR
        `roleDescription` LIKE CONCAT('%', :query, '%')
    )
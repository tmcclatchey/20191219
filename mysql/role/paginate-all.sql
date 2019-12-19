SELECT
    `roleId`,
    `roleName`,
    `roleDescription`,
    `roleFlags`,
    `providerId`
FROM
    `role`
LIMIT
    :startingIndex, :recordLimit
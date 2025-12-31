const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");

const can = (permission) => {
  return permissions.includes(permission);
};

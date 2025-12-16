

export const validateRequired = (field = "This field") => [
  (v) => !!v || `${field} is required.`,
];

export const validateMinLength = (field, min) => [
  (v) => !!v || `${field} is required.`,
  (v) => (v && v.length >= min) || `${field} must be at least ${min} characters.`,
];

export const validateMaxLength = (field, max) => [
  (v) => !!v || `${field} is required.`,
  (v) => (v && v.length <= max) || `${field} must not exceed ${max} characters.`,
];

export const validatePassingScore = (max) => [
  (v) =>
    (v && v >= 1 && v <= max) ||
    `Passing score must be between 1 and ${max}.`,
];


export const validateEmail = () => [
  (v) => !!v || "Email is required.",
  (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || "Invalid email address.",
];

export const validatePassword = () => [
  (v) => !!v || "Password is required.",
  (v) => (v && v.length >= 6) || "Password must be at least 6 characters.",
];


export const validateConfirmPassword = (password) => [
  (v) => !!v || "Confirm password is required.",
  (v) => v === password.value || "Confirm password mismatch.",
];

export const validatePhoneNo = () => [
  (v) => !!v || "Phone number is required.",
  (v) => /^[0-9]{10,15}$/.test(v) || "Enter a valid phone number.",
];


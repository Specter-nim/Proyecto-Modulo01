import {
  get,
  del,
  post,
  postID,
  put,
} from '../../../services/api/Axios/MethodsGeneral';


export const login = async (data) => {
  const response = await post('auth/login', data);
  return response;
};


export const validateCode = async (code, id) => {
  const response = await postID('auth/verify-email', id, code);
  return response;
};

export const changePassword = async (data) => {
  const response = await put('auth/reset-password', data);
  return response;
};
export const logOut = async () => {
  const response = await del('auth/logout');
  return response;
};

export const sendVerifyCode = async (id) => {
  const response = await postID('auth/sendVerifyCode', id, { type_email: 0 });
  return response;
};

export const getRole = async () => {
  const response = await get('auth/permissions');
  return response;
};

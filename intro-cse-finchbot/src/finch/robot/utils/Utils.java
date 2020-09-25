package finch.robot.utils;

/**
 * Created by yoseph on 5/23/2016.
 */
public class Utils {
    public static boolean isNumeric(String s) {
        try {
            Double.parseDouble(s);
            return true;
        } catch (Exception e) {
            return false;
        }
    }
}

package finch.robot.subsystems;

import finch.robot.Robot;
import finch.robot.framework.Subsystem;

import java.awt.*;

/**
 * Created by yosephsa on 10/2/2016.
 */
public class LED extends Subsystem {

    @Override
    public void init() {

    }

    public void setLED(Color color) {
        Robot.getInstance().getFinch().setLED(color);
    }

    public void setLED(Color color, int duration) {
        Robot.getInstance().getFinch().setLED(color, duration);
    }

    public void setLED(int red, int green, int blue) {
        Robot.getInstance().getFinch().setLED(red, green, blue);
    }

    public void setLED(int red, int green, int blue, int duration) {
        Robot.getInstance().getFinch().setLED(red, green, blue, duration);
    }

    @Override
    public void end() {

    }
}
